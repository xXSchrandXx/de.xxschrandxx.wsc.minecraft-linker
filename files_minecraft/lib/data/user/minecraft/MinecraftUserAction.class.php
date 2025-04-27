<?php

namespace minecraft\data\user\minecraft;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * MinecraftUser Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Minecraft
 */
class MinecraftUserAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = MinecraftUserEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['user.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['user.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    public function delete()
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$this->getObjectIDs()]);
        $userToMinecraftUserList->readObjects();
        $userToMinecraftUsers = $userToMinecraftUserList->getObjects();
        (new UserToMinecraftUserAction($userToMinecraftUsers, 'delete'))->executeAction();

        return parent::delete();
    }
}
