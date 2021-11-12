<?php

namespace wcf\page;

use wcf\data\user\minecraft\MinecraftList;
use wcf\system\exception\IllegalLinkException;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

class MinecraftIDListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_ENABLED','MINECRAFT_SYNC_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.minecraftSynchronisation.canManage'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = MinecraftList::class;

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $sortField = 'minecraftID';

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

        if (!(MINECRAFT_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        $this->objectList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.minecraftSection.minecraftIDList');

        parent::show();
    }

    public function assignVariables()
    {
        parent::assignVariables();
    }
}
