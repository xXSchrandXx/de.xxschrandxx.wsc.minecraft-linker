<?php

namespace wcf\form;

use wcf\page\MinecraftUserListPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\menu\user\UserMenu;
use wcf\system\minecraft\MinecraftLinkerHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

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
    public $activeMenuItem = 'wcf.user.menu.minecraftSection.minecraftUserList';

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * Liste der Spieler auf den Server(n)
     * @var array
     */
    protected $options = [];

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

        if ($this->formAction == 'edit') {
            return;
        }

        if (MINECRAFT_MAX_UUIDS == 0 || MINECRAFT_MAX_UUIDS <= WCF::getUser()->minecraftUUIDs) {
            HeaderUtil::delayedRedirect(
                LinkHandler::getInstance()->getControllerLink(MinecraftUserListPage::class),
                WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.error.maxReached'),
                2,
                'error'
            );
            exit;
        }

        $code = WCF::getSession()->getVar('mcCode');
        $title = WCF::getSession()->getVar('mcTitle');
        $minecraftUUID = WCF::getSession()->getVar('minecraftUUID');

        if (isset($minecraftUUID) && isset($code) && isset($title)) {
            HeaderUtil::delayedRedirect(
                LinkHandler::getInstance()->getControllerLink(MinecraftUserCheckForm::class),
                WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.error.alreadySend'),
                2,
                'error'
            );
            exit;
        }
    }

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        $children = [
            TextFormField::create('title')
                ->required()
                ->label('wcf.page.minecraftUserAdd.title')
                ->description('wcf.page.minecraftUserAdd.title.description')
                ->maximumLength(30)
                ->value('Default')
                ->available(MINECRAFT_MAX_UUIDS > 1)
        ];

        if ($this->formAction == 'create') {
            $this->mcsh = MinecraftLinkerHandler::getInstance();

            $this->readOptions();

            if (empty($this->options)) {
                HeaderUtil::delayedRedirect(
                    LinkHandler::getInstance()->getControllerLink(MinecraftUserListPage::class),
                    WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.error.noUnknownUsers'),
                    5,
                    'error'
                );
                exit;
            }

            \array_push(
                $children,
                SingleSelectionFormField::create('minecraftUUID')
                ->required()
                ->label('wcf.page.minecraftUserAdd.uuid')
                ->description('wcf.page.minecraftUserAdd.uuid.description')
                ->options($this->options, true, false)
                ->filterable()
                ->addValidator(new FormFieldValidator('sendCode', function (SingleSelectionFormField $field) {
                    $this->code = bin2hex(\random_bytes(4));

                    /** @var array */
                    $response = $this->mcsh->sendCode(
                        $this->form->getData()['data']['minecraftUUID'],
                        $this->code
                    );
                    if ($response['statusCode'] == 400) {
                        new FormFieldValidationError(
                            'sendCode',
                            'wcf.page.minecraftUserAdd.error.sendCodeDynamic',
                            ['msg' => $response['status']]
                        );
                    } else if ($response['statusCode'] == 500) {
                        $field->addValidationError(
                            new FormFieldValidationError(
                                'sendCode',
                                'wcf.page.minecraftUserAdd.error.sendCode'
                            )
                        );
                    }
                }))
            );
        }

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren($children)
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        if ($this->formAction == 'edit') {
            return;
        }

        WCF::getSession()->register('mcCode', $this->code);

        $title = 'Default';
        if (isset($this->form->getData()['data']['title'])) {
            $title = $this->form->getData()['data']['title'];
        }

        WCF::getSession()->register('mcTitle', $title);
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

        if ($this->formAction == 'edit') {
            return;
        }

        $this->readOptions();

        HeaderUtil::delayedRedirect(
            LinkHandler::getInstance()->getControllerLink(MinecraftUserCheckForm::class),
            WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftUserAdd.success'),
            2
        );
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

    protected function readOptions()
    {
        $unknownUsers = $this->mcsh->getUnknownMinecraftUsers();

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
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'showMinecraftLinkerBranding' => true
        ]);
    }
}
