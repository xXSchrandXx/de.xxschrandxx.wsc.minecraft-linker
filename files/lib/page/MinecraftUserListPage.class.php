<?php

namespace wcf\page;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToUserMinecraftList;
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
    public function initObjectList()
    {
        parent::initObjectList();

        $userToUserMinecraftList = new UserToUserMinecraftList();
        $userToUserMinecraftList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
        $userToUserMinecraftList->readObjectIDs();

        $this->objectList->setObjectIDs($userToUserMinecraftList->getObjectIDs());
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
