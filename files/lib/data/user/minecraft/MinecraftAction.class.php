<?php

namespace wcf\data\minecraft;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Minecraft Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class MinecraftAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.minecraftSynchronisation.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.minecraftSynchronisation.canManage'];

    /**
     * @inheritDoc
     * @todo
     */
    public function create() {
        parent::create();
    }

    /**
     * @inheritDoc
     * @todo
     */
    public function delete() {
        parent::delete();
    }
}