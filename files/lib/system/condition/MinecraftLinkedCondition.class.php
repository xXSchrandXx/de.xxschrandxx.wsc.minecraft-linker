<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToUserMinecraft;
use wcf\data\user\minecraft\UserToUserMinecraftList;
use wcf\system\WCF;

/**
 * MinecraftLinker linked condition class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftLinkedCondition extends AbstractCheckboxCondition implements IUserCondition, IObjectListCondition
{
    use TObjectListUserCondition;

    /**
     * @inheritDoc
     */
    protected $fieldName = 'minecraftLinked';

    /**
     * @inheritDoc
     */
    public function checkUser(Condition $condition, User $user)
    {
        $userToUserMinecraftList = new UserToUserMinecraftList();
        $userToUserMinecraftList->getConditionBuilder()->add('userID = ?', [$user->userID]);
        $userToUserMinecraftList->readObjectIDs();

        $userMinecraftList = new MinecraftUserList();
        $userMinecraftList->setObjectIDs($userToUserMinecraftList->getObjectIDs());

        if ($userMinecraftList->countObjects() === 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof UserList)) {
            throw new \InvalidArgumentException("Object list is no instance of '" . UserList::class . "', instance of '" . get_class($objectList) . "' given.");
        }

        if (isset($conditionData[$this->fieldName]) && $conditionData[$this->fieldName]) {
            $userToUserMinecraftList = new UserToUserMinecraftList();
            $userToUserMinecraftList->readObjectIDs();
            $objectList->setObjectIDs($userToUserMinecraftList->getObjectIDs());
        }

        $objectList->readObjects();
    }
}
