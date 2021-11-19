<?php

namespace wcf\acp\form;

use wcf\data\user\User;
use wcf\data\user\minecraft\MinecraftAction;
use wcf\data\user\minecraft\MinecraftList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\minecraft\MinecraftLinkerHandler;
use wcf\system\WCF;

class MinecraftIDAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_LINKER_ENABLED','MINECRAFT_LINKER_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.user.management';

    /**
     * @inheritDoc
     */
    public $objectActionClass = MinecraftAction::class;

    /**
     * Benutzer-Objekt
     *
     * @var User|null
     */
    protected $user;

    /**
     * @inheritDoc
     */
    public function checkModules()
    {
        parent::checkModules();

        if (!(MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY)) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $userID = 0;
        if (isset($_REQUEST['id'])) {
            $userID = (int)$_REQUEST['id'];
        }
        $this->user = new User($userID);
        if (!$this->user->userID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        $mcUsers = MinecraftLinkerHandler::getInstance()->getUnknownMinecraftUsers();

        $options = [];
        foreach ($mcUsers as $minecraftID => $uuidArray) {
            foreach ($uuidArray as $uuid => $name) {
                array_push($options, ['label' => $name, 'value' => $uuid, 'depth' => 0]);
            }
        }

        $fields = [];

        array_push($fields, TextFormField::create('title')
            ->label('wcf.acp.page.minecraftIDAdd.title')
            ->description('wcf.acp.page.minecraftIDAdd.title.description')
            ->maximumLength(30)
            ->value('Default')
            ->required()
        );

        if (empty($options)) {
            array_push($fields, TextFormField::create('minecraftUUID')
                ->label('wcf.acp.page.minecraftIDAdd.minecraftUUID')
                ->description('wcf.acp.page.minecraftIDAdd.minecraftUUID.description')
                ->minimumLength(36)
                ->maximumLength(36)
                ->addValidator(new FormFieldValidator('checkMinecraftUser', function (TextFormField $field) {
                    $minecraftList = new MinecraftList();
                    $minecraftList->getConditionBuilder()->add('minecraftUUID = ?', [$field->getValue()]);
                    $minecraftList->readObjects();
                    if (count($minecraftList)) {
                        $field->addValidationError(
                            new FormFieldValidationError('alreadyUsed', 'wcf.acp.page.minecraftIDAdd.minecraftUUID.error.alreadyUsed')
                        );
                    }
                }))
            );
        }
        else {
            array_push($fields, SingleSelectionFormField::create('minecraftUUID')
                ->label('wcf.acp.page.minecraftIDAdd.minecraftUUID')
                ->description('wcf.acp.page.minecraftIDAdd.minecraftUUID.description')
                ->options($options, true, false)
                ->filterable()
                ->addValidator(new FormFieldValidator('checkMinecraftUser', function (SingleSelectionFormField $field) {
                    $minecraftList = new MinecraftList();
                    $minecraftList->getConditionBuilder()->add('minecraftUUID = ?', [$field->getValue()]);
                    $minecraftList->readObjects();
                    if (count($minecraftList)) {
                        $field->addValidationError(
                            new FormFieldValidationError('alreadyUsed', 'wcf.acp.page.minecraftIDAdd.minecraftUUID.error.empty')
                        );
                    }
                }))
            );
        }

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren($fields)
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction == 'create') {
            $this->additionalFields['userID'] = $this->user->userID;
            $this->additionalFields['createdDate'] = \TIME_NOW;
        }

        parent::save();
    }

    /**
     * @inheritDoc
     */
    public function setFormAction()
    {
        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, ['id' => $this->user->userID]));
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'userID' => $this->user->userID
        ]);
    }
}
