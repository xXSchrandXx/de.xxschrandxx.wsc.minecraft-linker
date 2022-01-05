<?php

namespace wcf\data\user\minecraft;

use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\event\EventHandler;
use wcf\system\WCF;

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
    protected $permissionsCreate = ['user.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['user.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    public function create()
    {
        parent::create();
        $this->updateUUIDAmount($this->parameters['data']['userID']);
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        parent::delete();
        $userIDs = [];
        foreach ($this->getObjects() as $object) {
            array_push($userIDs, $object->userID);
        }
        foreach ($userIDs as &$userID) {
            $this->updateUUIDAmount($userID);
        }
    }

    /**
     * Aktuallisiert die minecraftUUIDs vom User.
     */
    public function updateUUIDAmount($userID)
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('userID = ?', [$userID]);
        $minecraftUserList->readObjects();

        $editor = new UserEditor(new User($userID));
        $editor->update(['minecraftUUIDs' => count($minecraftUserList)]);
    }
}
