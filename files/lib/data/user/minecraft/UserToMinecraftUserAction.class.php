<?php

namespace wcf\data\user\minecraft;

use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\user\UserAction;

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
