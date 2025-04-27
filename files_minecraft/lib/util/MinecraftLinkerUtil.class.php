<?php

namespace minecraft\util;

use Exception;
use minecraft\data\user\minecraft\MinecraftUser;
use minecraft\data\user\minecraft\MinecraftUserList;
use minecraft\data\user\minecraft\UserToMinecraftUserList;
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
     * Gets the MinecraftUser from given UUID.
     * @param string $uuid UUID
     * @return ?MinecraftUser
     */
    public static function getMinecraftUser(string $uuid): ?MinecraftUser
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$uuid]);
        if ($minecraftUserList->countObjects() !== 1) {
            return null;
        }
        $minecraftUserList->readObjects();
        return $minecraftUserList->getSingleObject();
    }

    /**
     * Gets the User from given UUID.
     * @param string $uuid UUID
     * @return ?User
     */
    public static function getUser(string $uuid): ?User
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$uuid]);
        if ($minecraftUserList->countObjects() !== 1) {
            return null;
        }
        $minecraftUserList->readObjectIDs();
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$minecraftUserList->getObjectIDs()]);
        if ($userToMinecraftUserList->countObjects() !== 1) {
            return null;
        }
        $userToMinecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\UserToMinecraftUser */
        $userToMinecraftUser = $userToMinecraftUserList->getSingleObject();
        return new User($userToMinecraftUser->getUserID());
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
     * @throws Exception when no UnknownMinecraftUsers exist
     */
    public static function getLinkedMinecraftUser(): MinecraftUserList
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->readObjects();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

        $minecraftUserList = new MinecraftUserList();
        if (empty($userToMinecraftUserIDs)) {
            throw new Exception('No linked minecraft User.', 400);
        }
        $minecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
        return $minecraftUserList;
    }
}
