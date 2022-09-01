<?php

namespace wcf\data\user\minecraft;

use wcf\data\DatabaseObjectList;

/**
 * MinecraftUser List class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class MinecraftUserList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = MinecraftUser::class;
}
