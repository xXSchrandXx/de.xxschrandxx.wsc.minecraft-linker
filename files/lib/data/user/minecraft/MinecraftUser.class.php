<?php

namespace wcf\data\user\minecraft;

use wcf\data\DatabaseObject;

/**
 * MinecraftUser Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class MinecraftUser extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'user_minecraft';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'minecraftUserID';

    /**
     * ObjectId $minecraftUserID
     * NotNullInt10 $userID
     * Varchar 36 $minecraftUUID
     * Varchar|null 16 $minecraftName
     * Varchar 30 $title
     * NotNullInt10 createdDate
     */
}
