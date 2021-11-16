<?php

namespace wcf\form;

use wcf\data\user\UserAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\menu\user\UserMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

class MinecraftIDCheckForm extends AbstractFormBuilderForm
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

        if (!isset($_REQUEST['uuid']) && !isset($_REQUEST['code'])) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDAdd'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDCheck.error.wrongLink'));
            exit;
        }

        $this->title = $_REQUEST['title'];
        $this->minecraftUUID = $_REQUEST['uuid'];
        $this->code = $_REQUEST['code'];
    }

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        if (empty($this->title) || empty($this->minecraftUUID) || empty($this->code)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDAdd'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.code.expired'));
            exit;
        }

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('code')
                        ->label('wcf.page.minecraftIDCheckForm.form.code')
                        ->description('wcf.page.minecraftIDCheckForm.code.description')
                        ->addValidator(new FormFieldValidator('minecraftUUIDCheck', function (TextFormField $field) {
                            if (!\hash_equals($this->checkCode, $field->getValue())) {
                                $field->addValidationError(
                                    new FormFieldValidationError('wrongSecurityCode', 'wcf.page.minecraftIDCheckForm.code.error.wrongSecurityCode')
                                );
                            }

                            $minecraftList = new MinecraftList();
                            $minecraftList->getConditionBuilder()->add('minecraftUUID = ?', [$this->minecraftUUID]);
                            $minecraftList->readObjects();
                            if (count($minecraftList) > 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError('alreadyUsed', 'wcf.page.minecraftIDCheckForm.code.error.alreadyUsed')
                                );
                            }
                        }))
                        ->required()
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        $this->objectAction = new MinecraftAction([], 'create', ['data' => [
            'userID' => WCF::getUser()->userID,
            'minecraftUUID' => $this->minecraftUUID,
            'title' => $this->title,
            'createdDate' => TIME_NOW
        ]]);
        $this->objectAction->executeAction();

        if (MINECRAFT_ENABLE_ACTIVE_USER && WCF::getUser()->activationCode) {
            $objectAction = new UserAction([WCF::getUser()], 'enable', ['skipNotification' => true]);
            $objectAction->executeAction();
        }
        MinecraftLinkerHandler::getInstance()->linkUser(WCF::getUser());

        WCF::getSession()->unregister('__mcCode');
        WCF::getSession()->unregister('__mcTitle');
        WCF::getSession()->unregister('__mcUUID');

        $this->saved();
    }

    /**
     * @inheritDoc
     */
    public function saved()
    {
        parent::saved();

        HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDCheckForm.success'));
        exit;
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.minecraftSection.minecraftIDList');

        parent::show();
    }
}
