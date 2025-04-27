<?php

namespace minecraft\system\event\listener;

use wcf\acp\action\UserExportGdprAction;
use minecraft\data\user\minecraft\MinecraftUserList;
use minecraft\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\event\listener\IParameterizedEventListener;

class MinecraftLinkerExportGdprActionListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param UserExportGdprAction $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $user = $eventObj->user;

        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$user->getUserID()]);
        $userToMinecraftUserList->readObjectIDs();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

        if (empty($userToMinecraftUserIDs)) {
            $eventObj->data['de.xxschrandxx.wsc.minecraft-linker'] = [];
            return;
        }

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
        $minecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\MinecraftUser[] */
        $minecraftUsers = $minecraftUserList->getObjects();

        $minecraftLinkerData = [];
        foreach ($minecraftUsers as $minecraftUser) {
            $minecraftLinkerData[] = [
                'title' => $minecraftUser->getTitle(),
                'uuid' => $minecraftUser->getMinecraftUUID(),
                'name' => $minecraftUser->getMinecraftName(),
                'time' => $minecraftUser->getCreatdDate()
            ];
        }

        $eventObj->data['de.xxschrandxx.wsc.minecraft-linker'] = $minecraftLinkerData;
    }
}
