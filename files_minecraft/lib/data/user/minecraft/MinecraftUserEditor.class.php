<?php

namespace minecraft\data\user\minecraft;

use wcf\data\DatabaseObjectEditor;

/**
 * MinecraftUser Editor class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class MinecraftUserEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = MinecraftUser::class;
}
