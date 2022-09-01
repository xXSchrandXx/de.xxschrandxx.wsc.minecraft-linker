<?php

namespace wcf\acp\form;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * MinecraftUser edit via text acp form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftUserEditForm extends MinecraftUserAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $minecraftUserID = 0;
        if (isset($_REQUEST['id'])) {
            $minecraftUserID = (int)$_REQUEST['id'];
        }
        $this->formObject = new MinecraftUser($minecraftUserID);
        if (!$this->formObject->minecraftUserID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'userID' => $this->formObject->userID
        ]);
    }
}
