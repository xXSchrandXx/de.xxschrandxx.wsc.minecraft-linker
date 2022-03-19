<?php

namespace wcf\system\minecraft;

interface IMinecraftLinkerHandler
{
    /**
     * Gibt ein Array mit der UUID und dem Namen aller Spieler auf den Servern zurrück.
     * @return array
     */
    public function getOnlineMinecraftUsers();

    /**
     * Sendet einen Befehl an den Server.
     * @param string $command der Befehl der ausgeführt werden soll.
     * @return array|string Die Antwort des Servers. String wenn ServerType Vanilla, sonst Array.
     */
//    public function sendCommand(string $command);

    /**
     * Sendet den Code an die UUID
     * @param string $uuid minecraft uuid of the player
     * @param string|null $name minecraft name of the player
     * @param string $code verification code for the player
     * @return array An array with the response.
     */
    public function sendCode($uuid, $code);
}
