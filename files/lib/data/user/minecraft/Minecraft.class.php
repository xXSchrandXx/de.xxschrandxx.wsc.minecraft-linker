<?php

namespace wcf\data\minecraft;

use wcf\data\DatabaseObject;
use wcf\system\exception\MinecraftException;
use wcf\system\minecraft\IMinecraftHandler;
use wcf\system\minecraft\MinecraftConnectionHandler;

/**
 * Minecraft Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class Minecraft extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'user_minecraft';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'minecraftID';
}
