<?php

namespace wcf\system\minecraft;

use wcf\data\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftList;

abstract class AbstractMinecraftLinkerHandler implements IMinecraftLinkerHandler
{
    /**
     * @var Minecraft
     */
    protected $minecraft;

    /**
     * @inheritDoc
     */
    public function __construct(Minecraft $mc)
    {
        $this->minecraft = $mc;

        if (!$this->minecraft) {
            return;
        }
    }

    /**
     * Eine Liste aller Benutzer, die sich gerade auf dem Minecraft-Server befinden.
     * Gruppiert nach den Minecraft-APIs.
     * ['uuid' => 'name']
     * @var array
     */
    protected $onlineUsers = [];

    /**
     * @inheritDoc
     */
    public function getOnlineMinecraftUsers()
    {
        return $this->onlineUsers;
    }

    /**
     * @inheritDoc
     */
    public function sendCommand(string $command)
    {
    }

    /**
     * @inheritDoc
     */
    public function sendCode($uuid, $name, $code)
    {
    }
}
