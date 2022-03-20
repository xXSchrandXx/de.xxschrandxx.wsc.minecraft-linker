<?php

namespace wcf\data\user\minecraft;

use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\user\UserAction;

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
//    protected $requireACP = ['update'];

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
        $amount = \count($minecraftUserList);

        $user = new User($userID);
        $editor = new UserEditor($user);
        $editor->update(['minecraftUUIDs' => $amount]);

        if (MINECRAFT_ENABLE_ACTIVE_USER) {
            if ($user->pendingActivation()) {
                if ($amount != 0) {
                    //enable
                    $action = new UserAction([$user], 'enable');
                    $action->executeAction();
                }
            } else {
                if ($amount == 0) {
                    //disable
                    $action = new UserAction([$user], 'disable');
                    $action->executeAction();
                }
            }
        }
    }
}
