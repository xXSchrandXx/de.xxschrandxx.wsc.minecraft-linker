<?php

namespace wcf\data\user\minecraft;

use wcf\data\DatabaseObjectList;

/**
 * UserToUserMinecraft List class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class UserToMinecraftUserList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserToMinecraftUser::class;
}
