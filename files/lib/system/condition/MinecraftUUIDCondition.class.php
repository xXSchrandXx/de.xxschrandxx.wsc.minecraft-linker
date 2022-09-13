<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\WCF;

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
    protected $label = 'wcf.user.condition.minecraftLinker.isLinked';

    /**
     * @inheritDoc
     */
    protected function getLabel()
    {
        return WCF::getLanguage()->get($this->label);
    }

    /**
     * @inheritDoc
     */
    public function checkUser(Condition $condition, User $user)
    {
        $userToMinecraftUserList = new UserToMinecraftUserList();
        $userToMinecraftUserList->getConditionBuilder()->add('userID = ?', [$user->getUserID()]);
        $userToMinecraftUserList->readObjectIDs();
        $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();

        if (empty($userToMinecraftUserIDs)) {
            return false;
        }

        $userMinecraftList = new MinecraftUserList();
        $userMinecraftList->getConditionBuilder()->add('minecraftUserID IN (?) AND minecraftUUID = ?', [$userToMinecraftUserIDs, $this->fieldValue]);

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
                return;
            }
            $userToMinecraftUserList = new UserToMinecraftUserList();
            $objectList->getConditionBuilder()->add('minecraftUserID IN (?)', [$minecraftUserIDs]);
            $userToMinecraftUserList->readObjectIDs();
            $userToMinecraftUserIDs = $userToMinecraftUserList->getObjectIDs();
            if (empty($userToMinecraftUserIDs)) {
                return;
            }
            $objectList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
        }

        $objectList->readObjects();
    }
}
