<?php

namespace wcf\form;

use wcf\data\user\minecraft\MinecraftAction;
use wcf\system\exception\MinecraftException;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\menu\user\UserMenu;
use wcf\system\minecraft\MinecraftLinkerHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\JSON;
use wcf\form\AbstractForm;

class MinecraftIDAddForm extends AbstractForm
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
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftList';

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
     * Liste aller unbekannter User.
     * @var array
     */
    private $mcUsers = [];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $this->mcsh = MinecraftLinkerHandler::getInstance();

        $unknownUsers = $this->mcsh->getUnknownMinecraftUsers();

        if (empty($unknownUsers)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.error.noUnknownUsers'), 5, 'error');
            exit;
        }

        foreach ($unknownUsers as $minecraftID => $uuidArray) {
            $this->mcUsers = $this->mcUsers + $uuidArray;
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'mcUsers' => $this->mcUsers
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        $code = bin2hex(\random_bytes(4));

        $title = 'Default';
        if (MINECRAFT_MAX_UUIDS > 1 && isset($_POST['title'])) {
            $title = $_POST['title'];
        }
        if ($this->mcsh->sendCode($_POST['uuid'], null, $code)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDCheck', ['title' => $title, 'uuid' => $_POST['uuid'], 'code' => $code]), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.success'));
            exit;
        }
        else {
            $this->errorField = 'uuid';
            $this->errorType = "Couldn't send code.";
        }
    }
}
