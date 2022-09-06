<?php

namespace wcf\data\user\minecraft;

use wcf\data\DatabaseObject;

/**
 * UserToUserMinecraft Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class UserToMinecraftUser extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'user_to_user_minecraft';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'minecraftUserID';

    /**
     * NotNullInt10 $userID
     * ObjectId $minecraftUserID
     */
}