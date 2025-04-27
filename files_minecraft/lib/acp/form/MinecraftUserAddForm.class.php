<?php

namespace minecraft\acp\form;

use minecraft\data\user\minecraft\MinecraftUser;
use wcf\data\user\User;
use minecraft\data\user\minecraft\MinecraftUserAction;
use minecraft\data\user\minecraft\MinecraftUserEditor;
use minecraft\data\user\minecraft\MinecraftUserList;
use minecraft\data\user\minecraft\UserToMinecraftUser;
use minecraft\data\user\minecraft\UserToMinecraftUserAction;
use minecraft\data\user\minecraft\UserToMinecraftUserEditor;
use minecraft\data\user\minecraft\UserToMinecraftUserList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use minecraft\util\MinecraftLinkerUtil;

/**
 * MinecraftUser add via text acp form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftUserAddForm extends AbstractFormBuilderForm
{
    /**
     * @var \wcf\data\user\minecraft\MinecraftUser
     */
    public $formObject;

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
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if ($this->formAction == 'create') {
            $userID = 0;
            if (isset($_REQUEST['id'])) {
                $userID = (int)$_REQUEST['id'];
            }
            $this->user = new User($userID);
            if (!$this->user->getUserID()) {
                throw new IllegalLinkException();
            }
        } else {
            $minecraftUserID = 0;
            if (isset($_REQUEST['id'])) {
                $minecraftUserID = (int)$_REQUEST['id'];
            }
            $this->formObject = new MinecraftUser($minecraftUserID);
            if (!$this->formObject->minecraftUserID) {
                throw new IllegalLinkException();
            }
        }
    }

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
    public function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TitleFormField::create()
                        ->required()
                        ->maximumLength(30)
                        ->value('Default'),
                    TextFormField::create('minecraftUUID')
                        ->required()
                        ->label('wcf.acp.form.minecraftUserAdd.minecraftUUID')
                        ->description('wcf.acp.form.minecraftUserAdd.minecraftUUID.description')
                        ->minimumLength(36)
                        ->maximumLength(36)
                        ->pattern(MinecraftLinkerUtil::UUID_PATTERN)
                        ->placeholder('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX')
                        ->addValidator(new FormFieldValidator('checkMinecraftUser', function (TextFormField $field) {
                            if ($this->formAction == 'edit') {
                                if ($field->getValue() == $this->formObject->getMinecraftUUID()) {
                                    return;
                                }
                            }
                            $minecraftUserList = new MinecraftUserList();
                            $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$field->getValue()]);
                            if ($minecraftUserList->countObjects() == 0) {
                                return;
                            }
                            $userToMinecraftUserList = new UserToMinecraftUserList();
                            $minecraftUserList->readObjectIDs();
                            $userToMinecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$minecraftUserList->getObjectIDs()]);
                            if ($userToMinecraftUserList->countObjects() == 0) {
                                $minecraftUserList->readObjects();
                                $minecraftUserEditor = new MinecraftUserEditor($minecraftUserList->getSingleObject());
                                $minecraftUserEditor->delete();
                                return;
                            }
                            $field->addValidationError(
                                new FormFieldValidationError('alreadyUsed', 'wcf.acp.form.minecraftUserAdd.minecraftUUID.error.alreadyUsed')
                            );
                        })),
                    TextFormField::create('minecraftName')
                        ->label('wcf.acp.form.minecraftUserAdd.minecraftName')
                        ->description('wcf.acp.form.minecraftUserAdd.minecraftName.description')
                        ->minimumLength(3)
                        ->maximumLength(16)
                        ->pattern('[0-9a-fA-F_]{3-16}')
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction == 'create') {
            $this->additionalFields['code'] = '';
            $this->additionalFields['createdDate'] = \TIME_NOW;
        }

        parent::save();
    }

    /**
     * @inheritDoc
     */
    public function saved()
    {
        if ($this->formAction == 'create') {
            $data = [
                ['data'] => [
                    'userID' => $this->user->getUserID(),
                    'minecraftUserID' => $this->objectAction->getReturnValues()['returnValues']->getObjectID()
                ]
            ];
            (new UserToMinecraftUserAction([], 'create', $data))->executeAction();
        }

        parent::saved();
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        if ($this->formAction == 'create') {
            WCF::getTPL()->assign([
               'userID' => $this->user->getUserID()
            ]);
        } else {
            $userToMinecraftUser = new UserToMinecraftUser($this->formObject->getObjectID());
            WCF::getTPL()->assign([
                'userID' => $userToMinecraftUser->getUserID()
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction()
    {
        if ($this->formAction == 'create') {
            $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, ['id' => $this->user->getUserID()]));
        } else {
            parent::setFormAction();
        }
    }
}
