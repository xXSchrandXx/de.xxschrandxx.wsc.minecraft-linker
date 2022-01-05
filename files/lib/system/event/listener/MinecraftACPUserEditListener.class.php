<?php

namespace wcf\system\event\listener;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\WCF;

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

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('userID = ?', [$eventObj->userID]);
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();

        WCF::getTPL()->assign([
            'minecraftUsers' => $minecraftUsers
        ]);
    }
}
