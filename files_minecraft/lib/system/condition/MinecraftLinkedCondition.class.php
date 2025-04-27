<?php

namespace minecraft\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use minecraft\data\user\minecraft\MinecraftUserList;
use minecraft\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\condition\AbstractCheckboxCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\condition\IUserCondition;
use wcf\system\condition\TObjectListUserCondition;
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

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
        if ($minecraftUserList->countObjects() === 0) {
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
            $userToMinecraftUserList = new UserToMinecraftUserList();
            $userToMinecraftUserList->readObjectIDs();
            $userToMinecraftUserIDs = $userToMinecraftUserList->readObjectIDs();
            if (empty($userToMinecraftUserIDs)) {
                return;
            }
            $objectList->getConditionBuilder()->add('minecraftUserID IN (?)', [$userToMinecraftUserIDs]);
        }

        $objectList->readObjects();
    }
}
