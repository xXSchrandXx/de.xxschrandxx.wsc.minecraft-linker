<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;
use wcf\system\WCF;

class VanillaMinecraftLinkerHandler extends AbstractMinecraftLinkerHandler
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
            $result = $this->minecraft->getConnection()->call(MINECRAFT_COMMAND_LIST);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return $this->onlineUsers;
        }
        if ($result == null) {
            return $this->onlineUsers;
        } else if (empty($result)) {
            return $this->onlineUsers;
        }
        $response = null;
        if ($result['Response'] != 0) {
            return $this->onlineUsers;
        } else {
            $response = $result['CMD'];
        }
        if ($response == null) {
            return $this->onlineUsers;
        } else if (empty($response)) {
            return $this->onlineUsers;
        }
        $userStringListString = explode(':', $response, 2)[1];
        $userStringList = explode(', ', $userStringListString);
        foreach ($userStringList as &$userString) {
            $userStringArray = explode(' (', $userString, 2);
            if (count($userStringArray) != 2) {
                continue;
            }
            $uuid = str_replace(['(', ')'], '', $userStringArray[1]);
            $name = str_replace(['(', ')'], '', $userStringArray[0]);
            $this->onlineUsers += [$uuid => $name];
        }
        return $this->onlineUsers;
    }

    /**
     * @inheritDoc
     */
    public function sendCommand(string $command)
    {
        try {
            $result = $this->minecraft->getConnection()->call($command);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => 'Error while sending command.'];
        }
        if ($result['Response'] != 0) {
            return ['error' => true, 'message' => 'Response not for commands.'];
        }
        return $result['CMD'];
    }

    /**
     * @inheritDoc
     */
    public function sendCode($uuid, $name, $code)
    {
        if ($uuid == null) {
            return ['error' => true, 'message' => 'No uuid given'];
        }
        if ($code == null) {
            return ['error' => true, 'message' => 'No code given'];
        }
        if ($name == null) {
            return ['error' => true, 'message' => 'No name given'];
        }
        $option = str_replace(['{lang}wcf.minecraft.message{/lang}', '{lang}wcf.minecraft.hoverMessage{/lang}'], [WCF::getLanguage()->get('wcf.minecraft.message'), WCF::getLanguage()->get('wcf.minecraft.hoverMessage')], MINECRAFT_COMMAND_SENDCODE);
        $command = str_replace(['{$uuid}', '{$name}', '{$code}'], [$uuid, $name, $code], $option);
        try {
            $result = $this->minecraft->getConnection()->call($command);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if ($result['Response'] != 0) {
            return ['error' => true, 'message' => 'Response not for commands.'];
        } else {
            return ['error' => false];
        }
    }
}
