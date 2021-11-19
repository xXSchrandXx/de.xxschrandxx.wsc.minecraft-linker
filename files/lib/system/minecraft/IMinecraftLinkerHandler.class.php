<?php

namespace wcf\system\minecraft;

interface IMinecraftLinkerHandler
{
    /**
     * Baut diese Klasse auf und eröffnet die Verbindung.
     * @param $mc Minecraft
     */
    public function __construct($mc);

    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * @return array
     */
    public function getOnlineMinecraftUsers();

    /**
     * Sendet einen Befehl an den Server.
     * @param $command der Befehl der ausgeführt werden soll.
     * @return array|string Die Antwort des Servers. String wenn ServerType Vanilla, sonst Array.
     */
    public function sendCommand($command);

    /**
     * Sendet den Code an die UUID
     * @param $uuid
     * @param $name
     * @param $code
     * @return boolean Weather it was successful.
     */
    public function sendCode($uuid, $name, $code);
}
