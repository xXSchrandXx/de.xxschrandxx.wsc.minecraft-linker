<?php

namespace wcf\data\user\minecraft;

use wcf\data\DatabaseObjectEditor;

/**
 * UserToUserMinecraft Editor class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class UserToMinecraftUserEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserToMinecraftUser::class;
}
