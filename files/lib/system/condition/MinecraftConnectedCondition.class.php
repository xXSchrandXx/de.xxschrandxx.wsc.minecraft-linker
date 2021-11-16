<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

class MinecraftConnectedCondition extends AbstractCondition implements IUserCondition, IObjectListCondition
{
    use TObjectListUserCondition;

    protected $minecraftConnected;

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = [];

        if ($this->minecraftConnected !== null) {
            $data['minecraftConnected'] = $this->minecraftConnected;
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
	<dt>{$this->getLanguage('wcf.user.condition.minecraftLinker.connection')}</dt>
	<dd>
        <label>
            <input type="checkbox" name="minecraftConnected" value="1"{$this->checkValue()}>
            {$this->getLanguage('wcf.user.condition.minecraftLinker.connected')}
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
        if (isset($_POST['minecraftConnected']) && $_POST['minecraftConnected']) {
            $this->minecraftConnected = 1;
        }
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->minecraftConnected = null;
    }

    /**
     * @inheritDoc
     */
    public function setData(Condition $condition)
    {
        $this->minecraftConnected = $condition->minecraftConnected;
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

        if (isset($conditionData['minecraftConnected']) && $conditionData['minecraftConnected']) {
            $objectList->getConditionBuilder()->add('user_table.minecraftIdentities > 0');
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
    protected function getLanguage($var)
    {
        return WCF::getLanguage()->getDynamicVariable($var);
    }

    /**
     * @inheritDoc
     */
    protected function checkValue()
    {
        if ($this->minecraftConnected) {
            return ' checked';
        }

        return '';
    }
}
