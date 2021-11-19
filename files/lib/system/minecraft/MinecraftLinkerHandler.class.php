<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;
use wcf\system\SingletonFactory;
use wcf\util\StringUtil;

class MinecraftLinkerHandler extends SingletonFactory
{
    /**
     * Liste der minecraftIDs
     *
     * @var array
     */
    protected $minecraftIDs = [];

    /**
     * Liste der Minecrafts
     *
     * @var array
     */
    protected $minecrafts = [];

    /**
     * Baut die Klasse auf
     */
    public function init()
    {
        if (MINECRAFT_LINKER_IDENTITY) {
            $this->minecraftIDs = explode('\n', StringUtil::unifyNewlines(MINECRAFT_LINKER_IDENTITY));
        }

        if (empty($this->minecraftIDs)) {
            return;
        }

        $minecraftList = new \wcf\data\minecraft\MinecraftList();
        $minecraftList->setObjectIDs($this->minecraftIDs);
        $minecraftList->readObjects();
        $this->minecrafts = $minecraftList->getObjects();
    }


    /**
     * Gibt den geforderten Minecraft zurück.
     *
     * @param  int $minecraftID
     * @return Minecraft
     */
    public function getMinecraft($minecraftID)
    {
        if (empty($this->minecrafts[$minecraftID])) {
            if (ENABLE_DEBUG_MODE) {
                throw new MinecraftException('found no minecraft with this id');
            }
            return null;
        }
        return $this->minecrafts[$minecraftID];
    }

    /**
     * Gibt alle Minecrafts zurück.
     * @return array
     */
    public function getMinecrafts()
    {
        return $this->minecrafts;
    }

    /**
     * Gibt den Handler zurück.
     * @param $minecraft
     * @return Minecraft
     */
    public function getHandler($minecraft)
    {
        if ($minecraft == null) {
            throw new MinecraftException('Unknown minecraft.');
        }
        $handler = null;
        switch ($minecraft->type) {
            case 'vanilla':
                $handler = new VanillaMinecraftLinkerHandler($minecraft);
                break;
            case 'spigot':
                $handler = new SpigotMinecraftLinkerHandler($minecraft);
                break;
            case 'bungee':
                $handler = new BungeeMinecraftLinkerHandler($minecraft);
        }
        if ($handler == null) {
            throw new MinecraftException('Unknown type.');
        }
        return $handler;
    }

    /**
     * Eine Liste aller Benutzer, die sich gerade auf dem Minecraft-Server befinden.
     * Gruppiert nach den Minecraft-APIs.
     * ['minecraftID' => ['uuid' => 'name']]
     * @var array
     */
    protected $onlineUsers = [];

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * @return array
     */
    public function getOnlineMinecraftUsers()
    {
        foreach ($this->minecrafts as &$minecraft) {
            $handler = $this->getHandler($minecraft);
            $tmpOnlineUsers = $handler->getOnlineMinecraftUsers();
            if (!empty($tmpOnlineUsers)) {
                $this->onlineUsers = $this->onlineUsers + [$minecraft->minecraftID => $tmpOnlineUsers];
            }
        }
        return $this->onlineUsers;
    }

    /**
     * Sends the code to the user.
     * @param $uuid minecraftUUID
     * @param $code code
     * @return bool Weather the code was sent successfully.
     */
    public function sendCode($uuid, $name, $code)
    {
        foreach ($this->getOnlineMinecraftUsers() as $minecraftID => $userArray) {
            if (array_key_exists($uuid, $userArray)) {
                if ($name == null) {
                    $name = $userArray[$uuid];
                }
                $minecraft = $this->getMinecraft($minecraftID);
                $handler = $this->getHandler($minecraft);
                return $handler->sendCode($uuid, $name, $code);
            }
        }
        return false;
    }

    /**
     * Eine Liste aller unbekannten Benutzer, die sich gerade auf dem Minecraft-Server befinden.
     * Gruppiert nach den Minecraft-APIs.
     * ['minecraftID' => ['uuid' => 'name']]
     * @var array
     */
    protected $unknownOnlineMinecraftUsers = [];

    /**
     * @inheritDoc
     */
    public function getUnknownMinecraftUsers()
    {
        if (!empty($this->unknownOnlineMinecraftUsers)) {
            return $this->unknownOnlineMinecraftUsers;
        }
        $allUsers = $this->getOnlineMinecraftUsers();
        if (empty($allUsers)) {
            return $this->unknownOnlineMinecraftUsers;
        }
        $savedUsersList = new \wcf\data\user\minecraft\MinecraftList();
        $savedUsersList->readObjects();
        $savedUsers = $savedUsersList->getObjects();
        $knownUsers = [];
        foreach ($savedUsers as &$savedUser) {
            array_push($knownUsers, $savedUser->minecraftUUID);
        }
        if (empty($knownUsers)) {
            $this->knownOnlineMinecraftUsers = $allUsers;
            return $this->knownOnlineMinecraftUsers;
        }
        foreach ($allUsers as $minecraftID => $users) {
            $newUsers = array_diff_key($users, array_flip($knownUsers));
            if (empty($newUsers)) {
                continue;
            }
            $this->unknownOnlineMinecraftUsers = $this->unknownOnlineMinecraftUsers + [$minecraftID => $newUsers];
        }
        return $this->unknownOnlineMinecraftUsers;
    }

    /**
     * Eine Liste aller bekannten Benutzer, die sich gerade auf dem Minecraft-Server befinden.
     * Gruppiert nach den Minecraft-APIs.
     * ['minecraftID' => ['uuid' => 'name']]
     * @var array
     */
    protected $knownOnlineMinecraftUsers = [];

    /**
     * @inheritDoc
     */
    public function getKnownMinecraftUsers()
    {
        if (!empty($this->knownOnlineMinecraftUsers)) {
            return $this->knownOnlineMinecraftUsers;
        }
        $allUsers = $this->getOnlineMinecraftUsers();
        if (empty($allUsers)) {
            return $this->knownOnlineMinecraftUsers;
        }
        $savedUsersList = new \wcf\data\user\minecraft\MinecraftList();
        $savedUsersList->readObjects();
        $savedUsers = $savedUsersList->getObjects();
        $knownUsers = [];
        foreach ($savedUsers as &$savedUser) {
            array_push($knownUsers, $savedUser->minecraftUUID);
        }
        if (empty($knownUsers)) {
            return $this->knownOnlineMinecraftUsers;
        }
        foreach ($allUsers as $minecraftID => $users) {
            $newUsers = array_intersect_key($users, array_flip($knownUsers));
            if (empty($newUsers)) {
                continue;
            }
            $this->knownOnlineMinecraftUsers = $this->knownOnlineMinecraftUsers + [$minecraftID => $newUsers];
        }
        return $this->knownOnlineMinecraftUsers;
    }
}
