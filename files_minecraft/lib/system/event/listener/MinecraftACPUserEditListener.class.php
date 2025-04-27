<?php

namespace minecraft\system\event\listener;

use minecraft\data\user\minecraft\MinecraftUserList;
use minecraft\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\event\listener\IParameterizedEventListener;
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
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$_REQUEST['id']]);
        $userToMinecraftUserList->readObjectIDs();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

        if (empty($userToMinecraftUserIDs)) {
            WCF::getTPL()->assign([
                'minecraftUsers' => []
            ]);
            return;
        }

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
        $minecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\MinecraftUser[] */
        $minecraftUsers = $minecraftUserList->getObjects();

        WCF::getTPL()->assign([
            'minecraftUsers' => $minecraftUsers
        ]);
    }
}
