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
      * Returns title
      * @return ?string
      */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns Minecraft-UUID
     * @return ?string
     */
    public function getMinecraftUUID()
    {
        return $this->minecraftUUID;
    }

    /**
     * Returns Minecraft-Name
     * @return ?string
     */
    public function getMinecraftName()
    {
        return $this->minecraftName;
    }

    /**
     * Returns code
     * @return ?string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns createdTimestamp
     * @return ?int
     */
    public function getCreatdDate()
    {
        return $this->createdDate;
    }
}
