<?php

namespace wcf\system\minecraft;

interface IMinecraftLinkerHandler
{
    
    /**
     * Baut diese Klasse auf.
     * @param $mc Minecraft
     */
    public function __construct($mc);

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * @return array
     */
    public function getOnlineMinecraftUsers();

    /**
     * Sendet den Code an die UUID
     * @param $uuid
     * @param $name
     * @param $code
     */
    public function sendCode($uuid, $name, $code);
}
