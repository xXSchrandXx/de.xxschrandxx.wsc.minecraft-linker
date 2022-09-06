<?php

namespace wcf\acp\form;

use wcf\data\user\User;
use wcf\data\user\minecraft\MinecraftUserAction;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;

/**
 * MinecraftUser add via text acp form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftUserAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_LINKER_ENABLED','MINECRAFT_LINKER_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.minecraftLinker.canManage'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.user.management';

    /**
     * @inheritDoc
     */
    public $objectActionClass = MinecraftUserAction::class;

    /**
     * Benutzer-Objekt
     * @var User|null
     */
    protected $user;

    /**
     * @inheritDoc
     */
    public function checkModules()
    {
        parent::checkModules();

        if (!(MINECRAFT_LINKER_ENABLED && MINECRAFT_LINKER_IDENTITY)) {
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
                ->appendChildren([
                    TitleFormField::create('title')
                        ->required()
                        ->label('wcf.page.minecraftUserAddACP.title')
                        ->description('wcf.page.minecraftUserAddACP.title.description')
                        ->maximumLength(30)
                        ->value('Default'),
                    TextFormField::create('minecraftUUID')
                        ->required()
                        ->label('wcf.page.minecraftUserAddACP.minecraftUUID')
                        ->description('wcf.page.minecraftUserAddACP.minecraftUUID.description')
                        ->minimumLength(36)
                        ->maximumLength(36)
                        ->pattern('^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$')
                        ->placeholder('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX')
                        ->addValidator(new FormFieldValidator('checkMinecraftUser', function (TextFormField $field) {
                            if ($this->formAction == 'edit') {
                                if ($field->getValue() == $this->formObject->minecraftUUID) {
                                    return;
                                }
                            }
                            $minecraftUserList = new MinecraftUserList();
                            $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$field->getValue()]);
                            $minecraftUserList->readObjects();
                            if (count($minecraftUserList)) {
                                $field->addValidationError(
                                    new FormFieldValidationError('alreadyUsed', 'wcf.page.minecraftUserAddACP.minecraftUUID.error.alreadyUsed')
                                );
                            }
                        })),
                    TextFormField::create('minecraftName')
                        ->label('wcf.page.minecraftUserAddACP.minecraftName')
                        ->description('wcf.page.minecraftUserAddACP.minecraftName.description')
                        ->minimumLength(3)
                        ->maximumLength(16)
                        ->pattern('[0-9a-fA-F_]{3-16}')
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction == 'create') {
            $this->additionalFields['userID'] = $this->user->userID;
            $this->additionalFields['createdDate'] = \TIME_NOW;
        }

        parent::save();
    }

    /**
     * @inheritDoc
     */
    public function setFormAction()
    {
        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, ['id' => $this->user->userID]));
    }
}
