<?php

namespace wcf\form;

use wcf\data\user\minecraft\MinecraftAction;
use wcf\system\exception\MinecraftException;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\menu\user\UserMenu;
use wcf\system\minecraft\MinecraftLinkerHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

class MinecraftIDAddForm extends AbstractFormBuilderForm
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
    public $loginRequired = true;

    /**
     * MinecraftLinkerHandler
     * @var MinecraftLinkerHandler
     */
    private $mcsh;

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
            throw new IllegalLinkException();
        }

        $code = WCF::getSession()->getVar('mcCode');
        $title = WCF::getSession()->getVar('mcTitle');
        $minecraftUUID = WCF::getSession()->getVar('minecraftUUID');

        if (isset($minecraftUUID) && isset($code) && isset($title)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDCheck'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.alreadySend'), 2, 'error');
            exit;
        }
    }

    public function createForm()
    {
        parent::createForm();

        $this->mcsh = MinecraftLinkerHandler::getInstance();

        $unknownUsers = $this->mcsh->getUnknownMinecraftUsers();

        if (empty($unknownUsers)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.error.noUnknownUsers'), 5, 'error');
            exit;
        }

        $options = [];
        foreach ($unknownUsers as $minecraftID => $uuidArray) {
            foreach ($uuidArray as $uuid => $name) {
                array_push($options, ['label' => $name, 'value' => $uuid, 'depth' => 0]);
            }
        }

        $fields = [];

        if (MINECRAFT_MAX_UUIDS > 1) {
            array_push($fields, TextFormField::create('title')
                ->label('wcf.page.minecraftIDAdd.title')
                ->description('wcf.page.minecraftIDAdd.title.description')
                ->maximumLength(30)
                ->value('Default')
                ->required()
            );
        }

        array_push($fields, SingleSelectionFormField::create('minecraftUUID')
            ->label('wcf.page.minecraftIDAdd.uuid')
            ->description('wcf.page.minecraftIDAdd.uuid.description')
            ->options($options, true, false)
            ->filterable()
            ->required()
        );

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

        $code = bin2hex(\random_bytes(4));

        $title = 'Default';
        if (MINECRAFT_MAX_UUIDS > 1 && isset($this->form->getData()['data']['title'])) {
            $title = $this->form->getData()['data']['title'];
        }

        if (strlen($this->form->getData()['data']['minecraftUUID']) != 36) {
            $this->errorField = 'uuid';
            $this->errorType = 'Wrong UUID format.';
            return;
        }

        if ($this->mcsh->sendCode($this->form->getData()['data']['minecraftUUID'], null, $code)) {
            WCF::getSession()->register('mcCode', $code);
            WCF::getSession()->register('mcTitle', $title);
            WCF::getSession()->register('minecraftUUID', $this->form->getData()['data']['minecraftUUID']);
        } else {
            $this->errorField = 'uuid';
            $this->errorType = "Couldn't send code.";
        }
        $this->saved();
    }

    public function saved()
    {
        parent::saved();

        HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDCheck'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.success'), 2);
        exit;
    }
}
