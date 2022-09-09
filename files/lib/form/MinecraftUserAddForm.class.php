<?php

namespace wcf\form;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\UserToMinecraftUser;
use wcf\data\user\minecraft\UserToMinecraftUserAction;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;
use wcf\util\MinecraftLinkerUtil;

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
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $objectActionClass = UserToMinecraftUserAction::class;

    /**
     * Weather maxReached should be shown
     */
    protected $showMaxReached = false;

    /**
     * Weather noUnknownUsers should be shown
     */
    protected $showNoUnknownUsers = false;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if ($this->formAction === 'edit') {
            return;
        }

        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->getUserID()]);

        $this->showMaxReached = (MINECRAFT_MAX_UUIDS == 0 || MINECRAFT_MAX_UUIDS <= $userToMinecraftUserList->countObjects());
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        // set active tab
        UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.minecraftSection.minecraftUserList');

        parent::show();
    }

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        if ($this->formAction === 'edit') {
            return;
        }

        $this->readOptions();

        $this->showNoUnknownUsers = empty($this->options);

        if ($this->showNoUnknownUsers) {
            $this->form->addDefaultButton(false);
            return;
        }

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('title')
                        ->required()
                        ->label('wcf.form.minecraftUserAdd.title')
                        ->description('wcf.form.minecraftUserAdd.title.description')
                        ->maximumLength(30)
                        ->value('Default')
                        ->available(MINECRAFT_MAX_UUIDS > 1),
                    SingleSelectionFormField::create('minecraftUserID')
                        ->required()
                        ->label('wcf.form.minecraftUserAdd.minecraftUserID')
                        ->description('wcf.form.minecraftUserAdd.minecraftUserID.description')
                        ->options(
                            $this->options,
                            true,
                            false
                        )
                        ->filterable(),
                    TextFormField::create('code')
                        ->required()
                        ->label('wcf.form.minecraftUserAdd.code')
                        ->description('wcf.form.minecraftUserAdd.code.description')
                        ->addValidator(new FormFieldValidator('checkCode', function (TextFormField $field) {
                            $minecraftUserID = $this->form->getData()['data']['minecraftUserID'];
                            if ($minecraftUserID === null) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'noValidSelection'
                                    )
                                );
                                return;
                            }
                            $minecraftUser = new MinecraftUser($minecraftUserID);
                            if ($minecraftUser->getObjectID() === 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'noValidSelection'
                                    )
                                );
                                return;
                            }
                            $userToMinecraftUser = new UserToMinecraftUser($minecraftUser->getObjectID());
                            if ($userToMinecraftUser->getObjectID() !== 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'alreadyUsed',
                                        'wcf.form.minecraftUserAdd.code.error.alreadyUsed'
                                    )
                                );
                                return;
                            }
                            if (!hash_equals($minecraftUser->getCode(), $field->getValue())) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'wrongSecurityCode',
                                        'wcf.form.minecraftUserAdd.code.error.wrongSecurityCode'
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
        if ($this->formAction === 'edit') {
            parent::save();
            return;
        }

        $this->additionalFields['userID'] = WCF::getUser()->getUserID();

        $title = 'Default';
        if (isset($this->form->getData()['data']['title'])) {
            $title = $this->form->getData()['data']['title'];
        }

        $this->form->getDataHandler()->addProcessor(
            new VoidFormDataProcessor(
                'title',
                true
            )
        );
        $this->form->getDataHandler()->addProcessor(
            new VoidFormDataProcessor(
                'code',
                true
            )
        );

        $minecraftUser = new MinecraftUser($this->form->getData()['data']['minecraftUserID']);
        $editor = new MinecraftUserEditor($minecraftUser);
        $editor->update([
            'title' => $title,
            'createdDate' => \TIME_NOW
        ]);

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

        $minecraftUserList = MinecraftLinkerUtil::getUnlinkedMinecraftUser();
        $minecraftUserList->readObjects();
        /** @var MinecraftUser[] */
        $minecraftUsers = $minecraftUserList->getObjects();

        foreach ($minecraftUsers as $minecraftUserID => $minecraftUser) {
            \array_push($this->options, ['label' => $minecraftUser->getMinecraftName(), 'value' => $minecraftUserID, 'depth' => 0]);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'showMaxReached' => $this->showMaxReached,
            'showNoUnknownUsers' => $this->showNoUnknownUsers,
            'showMinecraftLinkerBranding' => true
        ]);
    }
}
