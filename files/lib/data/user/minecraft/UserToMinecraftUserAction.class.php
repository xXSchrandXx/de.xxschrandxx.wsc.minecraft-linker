<?php

namespace wcf\data\user\minecraft;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserProfile;
use wcf\util\UserRegistrationUtil;

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

    /**
     * @inheritDoc
     * @return \wcf\data\user\minecraft\UserToMinecraftUser
     */
    public function create()
    {
        /** @var \wcf\data\user\minecraft\UserToMinecraftUser */
        $userToMinecraftUser = parent::create();
        if (!MINECRAFT_ENABLE_ACTIVE_USER) {
            return $userToMinecraftUser;
        }

        $user = new User($userToMinecraftUser->getUserID());
        // skip not existing user
        if (!$user->userID) {
            return $userToMinecraftUser;
        }

        // skip activated user
        if (!$user->pendingActivation()) {
            return $userToMinecraftUser;
        }

        // activate user
        $action = new UserAction([$user], 'enable');
        $action->executeAction();

        return $userToMinecraftUser;
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        if (!MINECRAFT_ENABLE_DISABLE_USER) {
            return parent::delete();
        }
        if (empty($this->objects)) {
            $this->readObjects();
        }

        // deactivate users without link
        $users = [];
        /** @var \wcf\data\user\minecraft\UserToMinecraftUser $object */
        foreach ($this->getObjects() as $userToMinecraftUser) {
            $user = new User($userToMinecraftUser->userID);
            // skip not existing user
            if (!$user->userID) {
                continue;
            }
            // skip admins
            if ($user->hasAdministrativeAccess()) {
                continue;
            }
            // check weather mandatory
            $userProfile = new UserProfile($user);
            if ($userProfile->getPermission('user.minecraftLinker.mandatory') != 1) {
                continue;
            }
            // check weather last userToMinecraftUser
            $userToMinecraftUserList = new UserToMinecraftUserList();
            $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$userToMinecraftUser->userID]);
            if ($userToMinecraftUserList->countObjects() > 1) {
                continue;
            }

            $users[] = $user;
        }
        // disable users
        if (!empty($users)) {
            $action = new UserAction($users, 'update', [
                'data' => [
                    'activationCode' => UserRegistrationUtil::getActivationCode()
                ],
                'removeGroups' => UserGroup::getGroupIDsByType([UserGroup::USERS]),
            ]);
            $action->executeAction();
            $action = new UserAction($users, 'addToGroups', [
                'groups' => UserGroup::getGroupIDsByType([UserGroup::GUESTS]),
                'deleteOldGroups' => false,
                'addDefaultGroups' => false
            ]);
            $action->executeAction();
        }

        return parent::delete();
    }
}
