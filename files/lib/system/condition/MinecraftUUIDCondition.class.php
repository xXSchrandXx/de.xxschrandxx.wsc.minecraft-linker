<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * MinecraftLinker uuid condition class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Condition
 */
class MinecraftUUIDCondition extends AbstractCondition implements IUserCondition, IObjectListCondition
{
    use TObjectListUserCondition;

    protected $minecraftUUID;

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = [];

        if ($this->minecraftUUID !== null) {
            $data['minecraftUUID'] = $this->minecraftUUID;
        }

        if (!empty($data)) {
            return $data;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getHTML()
    {
        return <<<HTML
<dl>
	<dt>{$this->getLanguage('wcf.user.condition.minecraftLinker.uuid')}</dt>
	<dd>
        <label>
            <input type="text" name="minecraftUUID" value="{$this->minecraftUUID}">
        </label>
	</dd>
</dl>
HTML;
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        if (isset($_POST['minecraftUUID']) && $_POST['minecraftUUID']) {
            $this->minecraftUUID = StringUtil::trim($_POST['minecraftUUID']);
        }
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->minecraftUUID = null;
    }

    /**
     * @inheritDoc
     */
    public function setData(Condition $condition)
    {
        $this->minecraftUUID = $condition->minecraftUUID;
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        // nothing to validate
    }

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof UserList)) {
            throw new \InvalidArgumentException("Object list is no instance of '" . UserList::class . "', instance of '" . get_class($objectList) . "' given.");
        }

        if (isset($conditionData['minecraftUUID'])) {
            $objectList->getConditionBuilder()->add('user_table.userID IN (SELECT DISTINCT userID FROM wcf' . WCF_N . '_user_minecraft WHERE minecraftUUID LIKE ?)', ['%' . $conditionData['minecraftUUID'] . '%']);
        }
        $objectList->readObjects();
    }

    /**
     * @inheritDoc
     */
    public function checkUser(Condition $condition, User $user)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getLanguage($var)
    {
        return WCF::getLanguage()->getDynamicVariable($var);
    }
}
