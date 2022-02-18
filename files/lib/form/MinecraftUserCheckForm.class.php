<?php

namespace wcf\form;

use wcf\data\user\UserAction;
use wcf\data\user\minecraft\MinecraftUserAction;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\menu\user\UserMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

class MinecraftUserCheckForm extends AbstractFormBuilderForm
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
    public $objectActionClass = MinecraftUserAction::class;

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * Titel der Minecraft-Identität
     * @var string
     */
    private $title;

    /**
     * MinecraftUUID des Benutzer
     * @var int
     */
    private $minecraftUUID;

    /**
     * Bestätigungs-Code
     * @var string
     */
    private $code;

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

        if (isset($_REQUEST['resend'])) {
            if ($_REQUEST['resend']) {
                WCF::getSession()->unregister('mcCode');
                WCF::getSession()->unregister('mcTitle');
                WCF::getSession()->unregister('minecraftUUID');
                if (MINECRAFT_NAME_ENABLED) {
                    WCF::getSession()->unregister('minecraftName');
                }

                HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftUserAdd'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserCheck.resend.success'), 2, 'info');
                exit;
            }
        }

        $this->code = WCF::getSession()->getVar('mcCode');
        $this->title = WCF::getSession()->getVar('mcTitle');
        $this->minecraftUUID = WCF::getSession()->getVar('minecraftUUID');
        if (MINECRAFT_NAME_ENABLED) {
            $this->minecraftName = WCF::getSession()->getVar('minecraftName');
        }

        if (!isset($this->minecraftUUID) || !isset($this->code) || !isset($this->title)) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        if (!isset($this->minecraftUUID) || !isset($this->code) || !isset($this->title)) {
            throw new IllegalLinkException();
        }

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('code')->required()
                        ->label('wcf.page.minecraftUserCheck.code')
                        ->description('wcf.page.minecraftUserCheck.code.description')
                        ->addValidator(new FormFieldValidator('minecraftUUIDCheck', function (TextFormField $field) {
                            if (!\hash_equals($this->code, $field->getValue())) {
                                $field->addValidationError(
                                    new FormFieldValidationError('wrongCode', 'wcf.page.minecraftUserCheck.code.error.wrongSecurityCode')
                                );
                            }
                            $minecraftUserList = new MinecraftUserList();
                            $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$this->minecraftUUID]);
                            $minecraftUserList->readObjects();
                            if (count($minecraftUserList) > 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError('used', 'wcf.page.minecraftUserCheck.code.error.alreadyUsed')
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
        $data = ['data' => [
            'userID' => WCF::getUser()->userID,
            'title' => $this->title,
            'minecraftUUID' => $this->minecraftUUID,
            'createdDate' => \TIME_NOW
        ]];
        if (MINECRAFT_NAME_ENABLED) {
            $data['data'] += ['minecraftName' => $this->minecraftName];
        }
        /** @var AbstractDatabaseObjectAction objectAction */
        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $this->formAction,
            $data
        );
        $this->objectAction->executeAction();

        $this->saved();
        WCF::getTPL()->assign('success', true);
    }

    /**
     * @inheritDoc
     */
    public function saved()
    {
        WCF::getSession()->unregister('mcCode');
        WCF::getSession()->unregister('mcTitle');
        WCF::getSession()->unregister('minecraftUUID');
        if (MINECRAFT_NAME_ENABLED) {
            WCF::getSession()->unregister('minecraftName');
        }

        $this->form->cleanup();
        $this->form->showSuccessMessage(true);

        HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftUserList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserCheck.success'));
        exit;
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        UserMenu::getInstance()->setActiveMenuItem($this->activeMenuItem);
        parent::show();
    }
}
