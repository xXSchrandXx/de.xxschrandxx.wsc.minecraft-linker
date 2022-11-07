<?php
namespace wcf\system\event\listener;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\data\user\UserProfile;

class MinecraftLinkerExportGdprActionListener implements IParameterizedEventListener {
    public function execute(/** @var UserExportGdprAction $eventObj */$eventObj, $className, $eventName, array &$parameters) {
        /** @var UserProfile $user */
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

        foreach ($minecraftUsers as $minecraftUser) {
            $eventObj->data['de.xxschrandxx.wsc.minecraft-linker'][$minecraftUser->getObjectID()] = [
                'Title' => $minecraftUser->getTitle(),
                'UUID' => $minecraftUser->getMinecraftUUID(),
                'Name' => $minecraftUser->getMinecraftName(),
                'Created' => $minecraftUser->getCreatdDate()
            ];
            }
    }
}
