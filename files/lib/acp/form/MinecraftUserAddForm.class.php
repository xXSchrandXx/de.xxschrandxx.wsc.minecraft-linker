<?php

namespace wcf\acp\form;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\User;
use wcf\data\user\minecraft\MinecraftUserAction;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUser;
use wcf\data\user\minecraft\UserToMinecraftUserEditor;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

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
                    TitleFormField::create('title')
                        ->required()
                        ->label('wcf.acp.page.minecraftUserAdd.title')
                        ->description('wcf.acp.page.minecraftUserAdd.title.description')
                        ->maximumLength(30)
                        ->value('Default'),
                    TextFormField::create('minecraftUUID')
                        ->required()
                        ->label('wcf.acp.page.minecraftUserAdd.minecraftUUID')
                        ->description('wcf.acp.page.minecraftUserAdd.minecraftUUID.description')
                        ->minimumLength(36)
                        ->maximumLength(36)
                        ->pattern('^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$')
                        ->placeholder('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX')
                        ->addValidator(new FormFieldValidator('checkMinecraftUser', function (TextFormField $field) {
                            if ($this->formAction == 'edit') {
                                if ($field->getValue() == $this->formObject->minecraftUUID) {
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
                                new FormFieldValidationError('alreadyUsed', 'wcf.acp.page.minecraftUserAdd.minecraftUUID.error.alreadyUsed')
                            );
                        })),
                    TextFormField::create('minecraftName')
                        ->label('wcf.acp.page.minecraftUserAdd.minecraftName')
                        ->description('wcf.acp.page.minecraftUserAdd.minecraftName.description')
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
            $userToMinecraftUserEditor = UserToMinecraftUserEditor::create([
                'userID' => $this->user->getUserID(),
                'minecraftUserID' => $this->objectAction->getReturnValues()['returnValues']->getObjectID()
            ]);
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
            $userToMinecraftUser = new UserToMinecraftUser($this->formObject->minecraftUserID);
            WCF::getTPL()->assign([
                'userID' => $userToMinecraftUser->userID
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
