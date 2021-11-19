<?php

namespace wcf\form;

use wcf\data\user\UserAction;
use wcf\data\user\minecraft\MinecraftAction;
use wcf\data\user\minecraft\MinecraftList;
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
    public $activeMenuItem = 'wcf.user.menu.minecraftSection.minecraftIDList';

     /**
     * @inheritDoc
     */
    public $objectActionClass = MinecraftAction::class;

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

        $this->code = WCF::getSession()->getVar('mcCode');
        $this->title = WCF::getSession()->getVar('mcTitle');
        $this->minecraftUUID = WCF::getSession()->getVar('minecraftUUID');

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
                    TextFormField::create('code')
                        ->label('wcf.page.minecraftIDCheckForm.code')
                        ->description('wcf.page.minecraftIDCheckForm.code.description')
                        ->addValidator(new FormFieldValidator('minecraftUUIDCheck', function (TextFormField $field) {
                            if (!\hash_equals($this->code, $field->getValue())) {
                                $field->addValidationError(
                                    new FormFieldValidationError('wrongCode', 'wcf.page.minecraftIDCheckForm.code.error.wrongSecurityCode')
                                );
                            }
                            $minecraftList = new MinecraftList();
                            $minecraftList->getConditionBuilder()->add('minecraftUUID = ?', [$this->minecraftUUID]);
                            $minecraftList->readObjects();
                            if (count($minecraftList) > 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError('used', 'wcf.page.minecraftIDCheckForm.code.error.alreadyUsed')
                                );
                            }
                        }))
                        ->required()
                ])
        );
    }

    public function save()
    {
        /** @var AbstractDatabaseObjectAction objectAction */
        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $this->formAction,
            ['data' => [
                'userID' => WCF::getUser()->userID,
                'title' => $this->title,
                'minecraftUUID' => $this->minecraftUUID,
                'createdDate' => \TIME_NOW
            ]]
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

        $this->form->cleanup();
        $this->form->showSuccessMessage(true);

        HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDCheck.success'));
        exit;
    }
}
