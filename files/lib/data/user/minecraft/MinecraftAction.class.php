<?php

namespace wcf\data\user\minecraft;

use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\user\minecraft\MinecraftList;
use wcf\system\event\EventHandler;
use wcf\system\WCF;

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
        foreach($userIDs as &$userID) {
            $this->updateUUIDAmount($userID);
        }
    }

    /**
     * Aktuallisiert die minecraftUUIDs vom User.
     */
    public function updateUUIDAmount($userID)
    {
        $minecraftList = new MinecraftList();
        $minecraftList->getConditionBuilder()->add('userID = ?', [$userID]);
        $minecraftList->readObjects();

        $editor = new UserEditor(new User($userID));
        $editor->update(['minecraftUUIDs' => count($minecraftList)]);
    }
}
