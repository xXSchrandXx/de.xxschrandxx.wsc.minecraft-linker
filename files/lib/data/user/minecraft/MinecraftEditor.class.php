<?php

namespace wcf\data\user\minecraft;

use wcf\data\DatabaseObjectEditor;

/**
 * Minecraft Editor class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class MinecraftEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Minecraft::class;
}
