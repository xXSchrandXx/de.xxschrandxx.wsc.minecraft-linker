<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

class MinecraftLinkedCondition extends AbstractCondition implements IUserCondition, IObjectListCondition
{
    use TObjectListUserCondition;

    protected $minecraftLinked;

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = [];

        if ($this->minecraftLinked !== null) {
            $data['minecraftLinked'] = $this->minecraftLinked;
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
	<dt>{$this->getLanguage('wcf.user.condition.minecraftLinker.minecraftLinked')}</dt>
	<dd>
        <label>
            <input type="checkbox" name="minecraftLinked" value="1"{$this->checkValue()}>
            {$this->getLanguage('wcf.user.condition.minecraftLinker.isLinked')}
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
        if (isset($_POST['minecraftLinked']) && $_POST['minecraftLinked']) {
            $this->minecraftLinked = 1;
        }
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->minecraftLinked = null;
    }

    /**
     * @inheritDoc
     */
    public function setData(Condition $condition)
    {
        $this->minecraftLinked = $condition->minecraftLinked;
    }

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof UserList)) {
            throw new \InvalidArgumentException("Object list is no instance of '" . UserList::class . "', instance of '" . get_class($objectList) . "' given.");
        }

        if (isset($conditionData['minecraftLinked']) && $conditionData['minecraftLinked']) {
            $objectList->getConditionBuilder()->add('user_table.minecraftUUIDs > 0');
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

    /**
     * @inheritDoc
     */
    public function checkValue()
    {
        if ($this->minecraftLinked) {
            return ' checked';
        }

        return '';
    }
}
