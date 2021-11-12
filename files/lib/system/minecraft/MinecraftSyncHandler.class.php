<?php

namespace wcf\system\minecraft;

use wcf\util\StringUtil;

class MinecraftSyncHandler extends AbstractMultipleMinecraftHandler
{
    /**
     * Baut diese Klasse auf.
     */
    public function init()
    {
        if (MINECRAFT_SYNC_IDENTITY) {
            $this->minecraftIDs = explode('\n', StringUtil::unifyNewlines(MINECRAFT_SYNC_IDENTITY));
        }

        parent::init();
    }

    /**
     * Eine Liste aller Benutzer, die sich gerade auf dem Minecraft-Server befinden.
     * Gruppiert nach den Minecraft-APIs.
     * ['minecraftID' => ['uuid' => 'name']]
     * @var array
     */
    protected $onlineUsers;

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * @return array
     */
    public function getOnlineMinecraftUsers()
    {
        if ($this->onlineUsers != null) {
            return $this->onlineUsers;
        }
        foreach ($this->minecrafts as &$minecraft) {
            $result = null;
            try {
                $result = $minecraft->getConnection()->call('list uuids');
            } catch (MinecraftException $e) {
                continue;
            }
            if ($result == null) {
                continue;
            }
            $userStringListString = explode(':', $result, 1)[1];
            $userStringList = explode(', ', $userListString);
            $users = [];
            foreach ($userStringList as &$userString) {
                $userStringArray = explode(' ', $userString, 1);
                $uuid = str_replace(['(', ')'], '', $userStringArray[1]);
                $name = $userStringArray[0];
                array_push($users, [$uuid => $name]);
            }
            array_push($this->onlineUsers, [$minecraft->minecraftID, $users]);
        }
        return $this->onlineUsers;
    }

    /**
     * Eine Liste aller unbekannten Benutzer, die sich gerade auf dem Minecraft-Server befinden.
     * Gruppiert nach den Minecraft-APIs.
     * ['minecraftID' => ['uuid' => 'name']]
     * @var array
     */
    protected $unknownOnlineMinecraftUsers;

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * Filtert schon benutzte UUIDs aus.
     * @return array
     */
    public function getUnknownMinecraftUsers()
    {
        if ($this->unknownOnlineMinecraftUsers != null) {
            return $this->unknownOnlineMinecraftUsers;
        }
        $allUsers = $this->getOnlineMinecraftUsers();
        $savedUsers = new \wcf\data\user\minecraft\MinecrarftList();
        $knownUsers = [];
        foreach ($savedUsers as &$savedUser) {
            array_push($knownUsers, $savedUser->minecraftUUID);
        }
        foreach ($allUsers as $minecraftID => $users) {
            $newUsers = array_diff_key($users, $knownUsers);
            array_push($this->unknownOnlineMinecraftUsers, [$minecraftID => $newUsers]);
        }
        return $this->unknownOnlineMinecraftUsers;
    }

    /**
     * Eine Liste aller bekannten Benutzer, die sich gerade auf dem Minecraft-Server befinden.
     * Gruppiert nach den Minecraft-APIs.
     * ['minecraftID' => ['uuid' => 'name']]
     * @var array
     */
    protected $knownOnlineMinecraftUsers;

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * Filtert schon unbenutzte UUIDs aus.
     * @return array
     */
    public function getKnownMinecraftUsers()
    {
        if ($this->knownOnlineMinecraftUsers != null) {
            return $this->knownOnlineMinecraftUsers;
        }
        $allUsers = $this->getOnlineMinecraftUsers();
        $savedUsers = new \wcf\data\user\minecraft\MinecrarftList();
        $knownUsers = [];
        foreach ($savedUsers as &$savedUser) {
            array_push($knownUsers, $savedUser->minecraftUUID);
        }
        foreach ($allUsers as $minecraftID => $users) {
            $newUsers = array_intersect_key($users, $knownUsers);
            array_push($this->knownOnlineMinecraftUsers, [$minecraftID => $newUsers]);
        }
        return $this->knownOnlineMinecraftUsers;
    }

    /**
     * Synchronisiert die Gruppe des Benutzers.
     * @var User
     * @throws MinecraftException
     */
    public function syncUser(User $user)
    {

    }
}
