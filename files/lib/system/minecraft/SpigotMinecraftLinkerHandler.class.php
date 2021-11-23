<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;
use wcf\system\WCF;
use wcf\util\JSON;

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
        $args = ['type' => 'list'];
        $jsonString = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        $result = null;
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonString);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return $this->onlineUsers;
        }
        if ($result == null) {
            return $this->onlineUsers;
        }
        if (empty($result)) {
            return $this->onlineUsers;
        }
        if ($result['Response'] != 0) {
            return $this->onlineUsers;
        }
        $response = [];
        try {
            if (!empty($result['S1'])) {
                $response = $response + JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response = $response + JSON::decode($result['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return $this->onlineUsers;
        }
        if (empty($response)) {
            return $this->onlineUsers;
        }
        if ($response['error']) {
            return $this->onlineUsers;
        }
        $this->onlineUsers = $response['message'];
        return $this->onlineUsers;
    }

    /**
     * @inheritDoc
     */
    public function sendCommand(string $command)
    {
        $args = [
            'type' => 'command',
            'content' => [
                'command' => $command
            ]
        ];
        $jsonSting = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonSting);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if ($result['Response'] != 0) {
            return ['error' => true, 'message' => 'Response not for commands.'];
        }
        $response = [];
        try {
            if (!empty($result['S1'])) {
                $response = $response + JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response = $response + JSON::decode($result['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function sendCode(string $uuid, string $name, string $code)
    {
        if ($uuid == null || $code == null) {
            return false;
        }
        if ($name == null) {
            $name = '';
        }
        $variables = ['uuid' => $uuid, 'name' => $name, 'code' => $code];
        $message = WCF::getLanguage()->getDynamicVariable('wcf.minecraft.message', $variables);
        $hoverMessage = WCF::getLanguage()->getDynamicVariable('wcf.minecraft.hoverMessage', $variables);
        $args = [
            'type' => 'sendCode',
            'content' => [
                'message' => $message,
                'uuid' => $uuid,
                'hoverMessage' => $hoverMessage,
                'code' => $code
            ]
        ];
        $jsonSting = JSON::encode($args, JSON_UNESCAPED_UNICODE);
        try {
            $result = $this->minecraft->getConnection()->call("wsclinker " . $jsonSting);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if ($result['Response'] != 0) {
            return ['error' => true, 'message' => 'Response not for commands.'];
        }
        $response = [];
        try {
            if (!empty($result['S1'])) {
                $response = $response + JSON::decode($result['S1']);
            }
            if (!empty($result['S2'])) {
                $response = $response + JSON::decode($result['S2']);
            }
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return ['error' => true, 'message' => $e->getMessage()];
        }
        if (empty($response)) {
            return ['error' => true, 'message' => 'Resonse empty'];
        }
        return $response;
    }
}
