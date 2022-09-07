<?php

namespace wcf\acp\form;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\system\exception\IllegalLinkException;

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
}
