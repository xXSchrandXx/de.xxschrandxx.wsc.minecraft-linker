<?php

namespace wcf\data\user\minecraft;

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
    protected $permissionsCreate = ['admin.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     * @todo
     */
    public function create()
    {
        parent::create();
    }

    /**
     * @inheritDoc
     * @todo
     */
    public function delete()
    {
        parent::delete();
    }
}
