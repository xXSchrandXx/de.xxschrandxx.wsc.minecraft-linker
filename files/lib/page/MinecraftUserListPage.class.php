<?php

namespace wcf\page;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\exception\IllegalLinkException;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

class MinecraftUserListPage extends MultipleLinkPage
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
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.user.menu.minecraftSection.minecraftUserList';

    /**
     * @inheritDoc
     */
    public $objectListClassName = MinecraftUserList::class;

    /**
     * @inheritDoc
     */
    public $sortField = 'minecraftUserID';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

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
    public function initObjectList()
    {
        parent::initObjectList();

        $this->objectList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
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
