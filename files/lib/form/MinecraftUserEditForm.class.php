<?php

namespace wcf\form;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\system\exception\IllegalLinkException;

/**
 * MinecraftUser edit form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Form
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

        if (MINECRAFT_MAX_UUIDS <= 1) {
            throw new IllegalLinkException();
        }

        $minecraftUserID = 0;
        if (isset($_REQUEST['id'])) {
            $minecraftUserID = (int)$_REQUEST['id'];
        }
        $this->formObject = new MinecraftUser($minecraftUserID);
        if (!$this->formObject->minecraftUserID) {
            throw new IllegalLinkException();
        }
    }
}
