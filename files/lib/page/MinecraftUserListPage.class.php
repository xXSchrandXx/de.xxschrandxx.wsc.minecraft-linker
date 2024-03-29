<?php

namespace wcf\page;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

/**
 * MinecraftUser list class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Page
 */
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
    public function show()
    {
        // set active tab
        UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.minecraftSection.minecraftUserList');

        parent::show();
    }

    /**
     * @inheritDoc
     */
    public function initObjectList()
    {
        parent::initObjectList();

        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->getUserID()]);
        $userToMinecraftUserList->readObjectIDs();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

        if (empty($userToMinecraftUserIDs)) {
            $this->objectList->getConditionBuilder()->add('minecraftUserID IN (?)', [[0]]);
            return;
        }

        $this->objectList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
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
