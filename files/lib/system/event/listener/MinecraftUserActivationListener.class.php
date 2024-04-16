<?php

namespace wcf\system\event\listener;

use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\data\user\UserAction;

class MinecraftUserActivationListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param UserAction $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if ($eventObj->getActionName() != 'enable') {
            return;
        }
        $objects = $eventObj->getObjects();

        foreach ($objects as $userEditor) {
            // check weather user is linked
            $userToMinecraftUserList = new UserToMinecraftUserList();
            $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$userEditor->getObjectID()]);
            if ($userToMinecraftUserList->countObjects() >= 1) {
                continue;
            }
            // do not enable user
            unset($userEditor);
        }
        $eventObj->setObjects($objects);
    }
}
