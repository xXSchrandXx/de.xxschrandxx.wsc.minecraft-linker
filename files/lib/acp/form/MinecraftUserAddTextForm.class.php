<?php

namespace wcf\acp\form;

use wcf\data\user\User;
use wcf\data\user\minecraft\MinecraftUserAction;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\minecraft\MinecraftLinkerHandler;
use wcf\system\WCF;

class MinecraftUserAddTextForm extends AbstractFormBuilderForm
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
        if (isset($_REQUEST['viaList'])) {
            $this->viaList = \filter_var($_REQUEST['viaList'], FILTER_VALIDATE_BOOLEAN);
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

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TitleFormField::create('title')
                        ->required()
                        ->label('wcf.page.minecraftUserAddACP.title')
                        ->description('wcf.page.minecraftUserAddACP.title.description')
                        ->maximumLength(30)
                        ->value('Default'),
                    TextFormField::create('minecraftUUID')
                        ->required()
                        ->label('wcf.page.minecraftUserAddACP.minecraftUUID')
                        ->description('wcf.page.minecraftUserAddACP.minecraftUUID.description')
                        ->minimumLength(36)
                        ->maximumLength(36)
                        ->pattern('^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$')
                        ->placeholder('XXXXXXXX-XXXX-4XXX-XXXX-XXXXXXXXXXXX')
                        ->addValidator(new FormFieldValidator('checkMinecraftUser', function (TextFormField $field) {
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
