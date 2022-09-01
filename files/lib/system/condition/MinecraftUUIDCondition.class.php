<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToUserMinecraftList;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * MinecraftLinker uuid condition class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftUUIDCondition extends AbstractTextCondition implements IUserCondition, IObjectListCondition
{
    use TObjectListUserCondition;

    /**
     * @inheritDoc
     */
    protected $fieldName = 'minecraftUUID';

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
        $userMinecraftList->getConditionBuilder()->add('minecraftUUID = ?', [$this->fieldValue]);

        if ($userMinecraftList->countObjects() === 1) {
            return true;
        } else {
            return false;
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
            $minecraftUserList = new MinecraftUserList();
            $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', $conditionData[$this->fieldName]);
            $minecraftUserList->readObjectIDs();

            $minecraftUserIDs = $minecraftUserList->getObjectIDs();
            if (empty($minecraftUserIDs)) {
                $userToUserMinecraftList = new UserToUserMinecraftList();
                $userToUserMinecraftList->setObjectIDs($minecraftUserIDs);
                $userToUserMinecraftList->readObjectIDs();
                $objectList->setObjectIDs($userToUserMinecraftList->getObjectIDs());
            }
        }

        $objectList->readObjects();
    }
}
