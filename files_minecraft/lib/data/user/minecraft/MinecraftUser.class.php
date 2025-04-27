<?php

namespace minecraft\data\user\minecraft;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;

/**
 * MinecraftUser Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 *
 * @property-read int $minecraftUserID
 * @property-read string $title
 * @property-read string $minecraftUUID
 * @property-read string $minecraftName
 * @property-read string $code
 * @property-read int $createdDate
 */
class MinecraftUser extends DatabaseObject implements ITitledObject
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
      * @inheritDoc
      */
    public function getTitle(): string
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
