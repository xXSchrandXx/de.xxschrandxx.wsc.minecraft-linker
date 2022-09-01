<?php

namespace wcf\data\user\minecraft;

use wcf\data\DatabaseObject;

/**
 * UserToUserMinecraft Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class UserToUserMinecraft extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'user_to_user_minecraft';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    /**
     * NotNullInt10 $userID
     * NotNullInt10 $minecraftUserID
     * VarChar $title
     */
}
