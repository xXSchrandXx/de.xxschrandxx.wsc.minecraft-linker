<?php

namespace wcf\acp\form;

use wcf\data\user\User;
use wcf\data\user\minecraft\MinecraftUserAction;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\minecraft\MinecraftLinkerHandler;
use wcf\system\WCF;

class MinecraftUserAddListForm extends AbstractFormBuilderForm
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
    public $objectActionClass = MinecraftUserAction::class;

    /**
     * Benutzer-Objekt
     * @var User|null
     */
    protected $user;

    /**
     * Liste der Spieler auf den Server(n)
     * @var array
     */
    protected $options = [];

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

        $unknownUsers = MinecraftLinkerHandler::getInstance()->getUnknownMinecraftUsers();

        if (empty($unknownUsers)) {
            return;
        }

        foreach ($unknownUsers as $minecraftID => $uuidArray) {
            foreach ($uuidArray as $uuid => $name) {
                $doppelt = false;
                foreach ($this->options as $id => $values) {
                    if ($values['value'] == $uuid) {
                        $doppelt = true;
                        break;
                    }
                }
                if ($doppelt) {
                    continue;
                }
                \array_push($this->options, ['label' => $name, 'value' => $uuid, 'depth' => 0]);
            }
        }

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TitleFormField::create('title')
                        ->required()
                        ->label('wcf.page.minecraftUserAddACP.title')
                        ->description('wcf.page.minecraftUserAddACP.title.description')
                        ->maximumLength(30)
                        ->value('Default'),
                    SingleSelectionFormField::create('minecraftUUID')
                        ->required()
                        ->label('wcf.page.minecraftUserAddACP.minecraftUUID')
                        ->options($this->options, true, false)
                        ->filterable()
                        ->addValidator(new FormFieldValidator('checkMinecraftUser', function (SingleSelectionFormField $field) {
                            if (!preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $field->getValue())) {
                                $field->addValidationError(
                                    new FormFieldValidationError('notUUID', 'wcf.page.minecraftUserAddACP.minecraftUUID.error.notUUID', ['uuid' => $field->getValue()])
                                );
                            }
                            $minecraftUserList = new MinecraftUserList();
                            $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$field->getValue()]);
                            $minecraftUserList->readObjects();
                            if (count($minecraftUserList)) {
                                $field->addValidationError(
                                    new FormFieldValidationError('alreadyUsed', 'wcf.page.minecraftUserAddACP.minecraftUUID.error.alreadyUsed')
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
        if ($this->formAction == 'create') {
            $this->additionalFields['userID'] = $this->user->userID;
            if (MINECRAFT_NAME_ENABLED) {
                foreach ($this->options as $id => $values) {
                    if ($values['value'] == $this->form->getData()['data']['minecraftUUID']) {
                        $this->additionalFields['minecraftName'] = $values['label'];
                        break;
                    }
                }
            }
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
            'userID' => $this->user->userID,
            'emptyList' => empty($this->options)
        ]);
    }
}
