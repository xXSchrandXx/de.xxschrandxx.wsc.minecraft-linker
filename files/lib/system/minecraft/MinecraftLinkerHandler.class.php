<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\exception\MinecraftException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;

/**
 * MinecraftLinker Handler class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
class MinecraftLinkerHandler extends AbstractMultipleMinecraftHandler implements IMinecraftLinkerHandler
{
    /**
     * Baut die Klasse auf
     */
    public function init(): void
    {
        if (MINECRAFT_LINKER_IDENTITY) {
            $this->minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_LINKER_IDENTITY));
        }
        parent::init();
    }

    /**
     * Sends the code to the user.
     * @param string $uuid minecraftUUID
     * @param string $code code
     * @return array
     */
    public function sendCode($uuid, $code)
    {
        foreach ($this->getOnlineMinecraftUsers() as $minecraftID => $userArray) {
            if (array_key_exists($uuid, $userArray)) {
                try {
                    $response = $this->call('POST', 'sendCode', [
                        'uuid' => $uuid,
                        'code' => $code,
                        'message' => WCF::getLanguage()->getDynamicVariable('wcf.minecraft.message', ['code' => $code]),
                        'hover' => WCF::getLanguage()->get('wcf.minecraft.hoverMessage')
                    ], $minecraftID);
                    if ($response === null) {
                        throw new MinecraftException("Could not get online users on server with id " . $minecraftID);
                    }
                    return JSON::decode($response->getBody());
                } catch (GuzzleException | SystemException $e) {
                    if (ENABLE_DEBUG_MODE) {
                        \wcf\functions\exception\logThrowable($e);
                    }
                    return [
                        'statusCode' => $e->getCode(),
                        'status' => $e->getMessage()
                    ];
                } catch (MinecraftException $e) {
                    if (ENABLE_DEBUG_MODE) {
                        \wcf\functions\exception\logThrowable($e);
                    }
                    return [
                        'statusCode' => $e->getCode(),
                        'status' => $e->getMessage()
                    ];
                }
            }
        }
        return [
            'statusCode' => 400,
            'status' => 'User Disconnected.'
        ];
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
        if (!empty($this->onlineUsers)) {
            return $this->onlineUsers;
        }
        foreach ($this->minecraftIDs as $minecraftID) {
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call('GET', 'list', [], $minecraftID);
                if ($response === null) {
                    throw new MinecraftException("Could not get online userss on server with id " . $minecraftID);
                }
                $responseBody = JSON::decode($response->getBody());
                $this->onlineUsers[$minecraftID] = $responseBody['user'];
            } catch (GuzzleException | SystemException | MinecraftException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
            }
        }
        return $this->onlineUsers;
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
        $savedUsersList = new MinecraftUserList();
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
            $this->unknownOnlineMinecraftUsers += [$minecraftID => $newUsers];
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
        $savedUsersList = new MinecraftUserList();
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
            $this->knownOnlineMinecraftUsers += [$minecraftID => $newUsers];
        }
        return $this->knownOnlineMinecraftUsers;
    }
}
