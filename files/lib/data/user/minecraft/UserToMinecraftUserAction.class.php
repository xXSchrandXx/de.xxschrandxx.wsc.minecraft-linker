<?php

namespace wcf\data\user\minecraft;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * UserToMinecraftUser Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class UserToMinecraftUserAction extends AbstractDatabaseObjectAction
{

    /**
     * @inheritDoc
     */
    protected $className = UserToMinecraftUserEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['user.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['user.minecraftLinker.canManage'];
}
