<?php

namespace wcf\util;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\data\user\User;

/**
 * MinecraftLinker util class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Util
 */
class MinecraftLinkerUtil extends MinecraftUtil
{
    /**
     * Gets the User from given UUID.
     * @param string $uuid UUID
     */
    public static function getUser(string $uuid): User
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$uuid]);
        $minecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\MinecraftUser */
        $minecraftUser = $minecraftUserList->getSingleObject();
        return new User($minecraftUser->getObjectID());
    }

    /**
     * Returns unread MinecraftUserList with unlinked minecraft users
     * @return MinecraftUserList
     */
    public static function getUnlinkedMinecraftUser(): MinecraftUserList
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->readObjects();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

        $minecraftUserList = new MinecraftUserList();
        if (!empty($userToMinecraftUserIDs)) {
            $minecraftUserList->getConditionBuilder()->add('minecraftUserID NOT IN (?)', [$userToMinecraftUserIDs]);
        }
        return $minecraftUserList;
    }

    /**
     * Returns unread MinecraftUserList with linked minecraft users
     * @return MinecraftUserList
     */
    public static function getLinkedMinecraftUser(): MinecraftUserList
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->readObjects();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

        $minecraftUserList = new MinecraftUserList();
        if (!empty($userToMinecraftUserIDs)) {
            $minecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
        }
        return $minecraftUserList;
    }
}
