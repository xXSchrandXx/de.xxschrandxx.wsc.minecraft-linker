<?php

namespace wcf\form;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;

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
    public $objectActionClass = MinecraftUserAction::class;

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
        if (!$this->formObject->getObjectID()) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChild(
                    TitleFormField::create()
                        ->required()
                        ->maximumLength(30)
                        ->value('Default')
                        ->available(MINECRAFT_MAX_UUIDS > 1)
                )
        );
    }
}
