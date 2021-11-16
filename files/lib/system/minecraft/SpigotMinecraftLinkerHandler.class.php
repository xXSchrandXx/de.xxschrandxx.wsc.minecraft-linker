<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;

class SpigotMinecraftLinkerHandler extends AbstractMinecraftLinkerHandler
{
    /**
     * @inheritDoc
     */
    public function getOnlineMinecraftUsers()
    {
        if (!empty($this->onlineUsers)) {
            return $this->onlineUsers;
        }
        $result = null;
        try {
            $result = $this->minecraft->getConnection()->call(MINECRAFT_COMMAND_SPIGOT_LIST);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                throw $e;
            }
            return $this->onlineUsers;
        }
        if ($result == null) {
            return $this->onlineUsers;
        }
        if (empty($result)) {
            return $this->onlineUsers;
        }
        $response = null;
        if ($result['Response'] != 0) {
            return $this->onlineUsers;
        }
        else {
            $response = $result['S1'] . $result['S2'];
        }
        if ($response == null) {
            return $this->onlineUsers;
        }
        if (empty($response)) {
            return $this->onlineUsers;
        }
        $userStringListString = explode(':', $response, 2)[1];
        $userStringList = explode(', ', $userStringListString);
        foreach ($userStringList as &$userString) {
            $userStringArray = explode(' (', $userString, 2);
            $uuid = substr(str_replace(['(', ')'], '', $userStringArray[1]), 0, 36);
            $name = $userStringArray[0];
            $this->onlineUsers = $this->onlineUsers + [$uuid => $name];
        }
        return $this->onlineUsers;
    }

    /**
     * @inheritDoc
     */
    public function sendCode($uuid, $name, $code)
    {
        $command = sprintf(MINECRAFT_COMMAND_SPIGOT_SENDCODE, $name, $code);
        try {
            $result = $this->minecraft->getConnection()->call($command);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                throw $e;
            }
            return false;
        }
        if ($result['Response'] == 0) {
            return true;
        }
        else {
            return false;
        }
    }
}
