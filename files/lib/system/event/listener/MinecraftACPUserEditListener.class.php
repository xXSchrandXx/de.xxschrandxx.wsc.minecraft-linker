<?php

namespace wcf\system\event\listener;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\WCF;

/**
 * MinecraftUser acp edit listener class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Event\Listener
 */
class MinecraftACPUserEditListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $this->$eventName($eventObj);
    }

    /**
     * @see AbstractPage::assignVariables()
     */
    public function assignVariables($eventObj)
    {
        if (!(MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.minecraftLinker.canManage')) {
            return;
        }

        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
        $userToMinecraftUserList->readObjectIDs();

        $userMinecraftList = new MinecraftUserList();
        $userMinecraftList->setObjectIDs($userToMinecraftUserList->getObjectIDs());
        $minecraftUsers = $userMinecraftList->readObjects();

        WCF::getTPL()->assign([
            'minecraftUsers' => $minecraftUsers
        ]);
    }
}
