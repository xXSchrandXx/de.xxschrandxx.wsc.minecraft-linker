<?php

namespace minecraft\acp\form;

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
