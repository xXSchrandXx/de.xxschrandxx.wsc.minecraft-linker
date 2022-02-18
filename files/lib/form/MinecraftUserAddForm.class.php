<?php

namespace wcf\form;

use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\MinecraftException;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\menu\user\UserMenu;
use wcf\system\minecraft\MinecraftLinkerHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

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
     * MinecraftLinkerHandler
     * @var MinecraftLinkerHandler
     */
    private $mcsh;

    /**
     * Liste der Spieler auf den Server(n)
     * @var array
     */
    protected $options = [];

    private $code;

    private $title;

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

        if (MINECRAFT_MAX_UUIDS == 0 || MINECRAFT_MAX_UUIDS <= WCF::getUser()->minecraftUUIDs) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftUserList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.error.maxReached'), 2, 'error');
            exit;
        }

        $code = WCF::getSession()->getVar('mcCode');
        $title = WCF::getSession()->getVar('mcTitle');
        $minecraftUUID = WCF::getSession()->getVar('minecraftUUID');

        if (isset($minecraftUUID) && isset($code) && isset($title)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftUserCheck'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.error.alreadySend'), 2, 'error');
            exit;
        }
    }

    public function createForm()
    {
        parent::createForm();

        $this->mcsh = MinecraftLinkerHandler::getInstance();

        $unknownUsers = $this->mcsh->getUnknownMinecraftUsers();

        if (empty($unknownUsers)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftUserList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.error.noUnknownUsers'), 5, 'error');
            exit;
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

        $fields = [];

        if (MINECRAFT_MAX_UUIDS > 1) {
            $titleField = TextFormField::create('title')
                ->required()
                ->label('wcf.page.minecraftUserAdd.title')
                ->description('wcf.page.minecraftUserAdd.title.description')
                ->maximumLength(30)
                ->value('Default');
            array_push($fields, $titleField);
        }

        $minecraftUUIDField = SingleSelectionFormField::create('minecraftUUID')
            ->required()
            ->label('wcf.page.minecraftUserAdd.uuid')
            ->description('wcf.page.minecraftUserAdd.uuid.description')
            ->options($this->options, true, false)
            ->filterable()
            ->addValidator(new FormFieldValidator('sendCode', function (SingleSelectionFormField $field) {
                $this->title = 'Default';
                if (MINECRAFT_MAX_UUIDS > 1 && isset($this->form->getData()['data']['title'])) {
                    $this->title = $field->getDocument()->getNodeById('title');
                }
                $this->code = bin2hex(\random_bytes(4));

                $response = $this->mcsh->sendCode($this->form->getData()['data']['minecraftUUID'], null, $this->code);

                if ($response['error'] == true) {
                    if (isset($response['message'])) {
                        $field->addValidationError(
                            new FormFieldValidationError('sendCode', 'wcf.page.minecraftUserAdd.error.sendCodeDynamic', ['msg' => $response['message']])
                        );
                    } else {
                        $field->addValidationError(
                            new FormFieldValidationError('sendCode', 'wcf.page.minecraftUserAdd.error.sendCode')
                        );
                    }
                }
            }));
        array_push($fields, $minecraftUUIDField);

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
        AbstractForm::save();

        WCF::getSession()->register('mcCode', $this->code);
        WCF::getSession()->register('mcTitle', $this->title);
        if (MINECRAFT_NAME_ENABLED) {
            foreach ($this->options as $id => $values) {
                if ($values['value'] == $this->form->getData()['data']['minecraftUUID']) {
                    WCF::getSession()->register('minecraftName', $values['label']);
                    break;
                }
            }
        }
        WCF::getSession()->register('minecraftUUID', $this->form->getData()['data']['minecraftUUID']);

        $this->saved();
    }

    public function saved()
    {
        parent::saved();

        HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftUserCheck'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.success'), 2);
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
