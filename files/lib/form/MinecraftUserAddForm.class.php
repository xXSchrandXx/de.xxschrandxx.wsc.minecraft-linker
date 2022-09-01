<?php

namespace wcf\form;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToUserMinecraftList;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\WCF;

/**
 * MinecraftUser add form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Form
 */
class MinecraftUserAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_LINKER_ENABLED','MINECRAFT_LINKER_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.user.menu.minecraftSection.minecraftUserList';

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $objectActionClass = UserToUserMinecraftAction::class;

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        $this->readOptions();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('title')
                        ->required()
                        ->label('wcf.page.minecraftUserAdd.title')
                        ->description('wcf.page.minecraftUserAdd.title.description')
                        ->maximumLength(30)
                        ->value('Default')
                        ->available(MINECRAFT_MAX_UUIDS > 1),
                    MultipleSelectionFormField::create('minecraftUserID')
                        ->required()
                        ->label('wcf.page.minecraftUserAdd.minecraftUserID')
                        ->description('wcf.page.minecraftUserAdd.minecraftUserID.description')
                        ->options(
                            $this->options,
                            true,
                            false
                        )
                        ->filterable(),
                    TextFormField::create('code')
                        ->required()
                        ->addValidator(new FormFieldValidator('checkCode', function (TextFormField $field) {
                            $minecraftUserID = $this->form->getData()['data']['minecraftUserID'];
                            $minecraftUser = new MinecraftUser($minecraftUserID);
                            if (!hash_equals($minecraftUser->code, $this->form->getData()['data']['code'])) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'checkCode',
                                        'wcf.page.minecraftUserAdd.error.checkCode'
                                    )
                                );
                            }
                        }))
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        $this->additionalFields['userID'] = WCF::getUser()->userID;

        $editor = new MinecraftUserEditor($this->form->getData()['data']['minecraftUserID']);
        $editor->update([
            'title' => $this->form->getData()['data']['title']
        ]);

        unset($this->form->getData()['data']['code']);
        unset($this->form->getData()['data']['title']);

        parent::save();
    }

    /**
     * Unlinked uuids
     * @var array
     */
    protected $options;

    /**
     * Lists unlinked uuids
     */
    protected function readOptions()
    {
        $this->options = [];

        $userToUserMinecraftList = new UserToUserMinecraftList();
        $userToUserMinecraftList->readObjects();
        $userToUserMinecrafts = $userToUserMinecraftList->getObjects();

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUserID != ?', array_values($userToUserMinecrafts));
        $minecraftUserList->readObjects();
        $minecraftUserIDs = $minecraftUserList->getObjects();

        foreach ($minecraftUserIDs as $minecraftUserID => $data) {
            \array_push($this->options, ['label' => $data['minecraftName'], 'value' => $minecraftUserID, 'depth' => 0]);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'showMinecraftLinkerBranding' => true
        ]);
    }
}