<?php

namespace wcf\system\minecraft;

class MinecraftSyncHandler extends AbstractMultipleMinecraftHandler
{
    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * @return array
     * @throws MinecraftException
     */
    public function getOnlineMinecraftUsers()
    {
        /**
         * ['minecraftID' => ['uuid' => 'name']]
         *  @var array
         */
        $groupedUsers = [];
        foreach ($minecrafts as &$minecraft) {
            $users = [];
            $result = $minecraft->getConnection()->call('list uuids');
            $userStringListString = explode(':', $result, 1)[1];
            $userStringList = explode(', ', $userListString);
            foreach ($userStringList as &$userString) {
                $userStringArray = explode(' ', $userString, 1);
                $uuid = str_replace(['(', ')'], '', $userStringArray[1]);
                $name = $userStringArray[0];
                array_push($users, [$uuid => $name]);
            }
            array_push($groupedUsers, [$minecraft->minecraftID, $users]);
        }
        return $groupedUsers;
    }

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * Filtert schon benutzte UUIDs aus.
     * @return array
     * @throws MinecraftException
     */
    public function getUnknownMinecraftUsers()
    {
        $allUsers = $this->getOnlineMinecraftUsers();
        $savedUsers = new \wcf\data\user\minecraft\MinecrarftList();
        $knownUsers = [];
        foreach ($savedUsers as &$savedUser) {
            array_push($knownUsers, $savedUser->minecraftUUID);
        }
        /**
         * ['minecraftID' => ['uuid' => 'name']]
         * @var array
         */
        $groupedUsers = [];
        foreach ($allUsers as $minecraftID => $users) {
            $newUsers = array_diff_key($users, $knownUsers);
            array_push($groupedUsers, [$minecraftID => $newUsers]);
        }
        return $groupedUsers;
    }

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * Filtert schon unbenutzte UUIDs aus.
     * @return array
     * @throws MinecraftException
     */
    public function getKnownMinecraftUsers()
    {
        $allUsers = $this->getOnlineMinecraftUsers();
        $savedUsers = new \wcf\data\user\minecraft\MinecrarftList();
        $knownUsers = [];
        foreach ($savedUsers as &$savedUser) {
            array_push($knownUsers, $savedUser->minecraftUUID);
        }
        /**
         * ['minecraftID' => ['uuid' => 'name']]
         * @var array
         */
        $groupedUsers = [];
        foreach ($allUsers as $minecraftID => $users) {
            $newUsers = array_intersect_key($users, $knownUsers);
            array_push($groupedUsers, [$minecraftID => $newUsers]);
        }
        return $groupedUsers;
    }
}
