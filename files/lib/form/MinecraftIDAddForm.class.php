<?php

namespace wcf\form;

use wcf\system\exception\MinecraftException;
use wcf\system\menu\user\UserMenu;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\JSON;
use wcf\form\AbstractForm;

class MinecraftIDAddForm extends AbstractForm
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_ENABLED','MINECRAFT_SYNC_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.minecraftSynchronisation.canManage'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftList';

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * MinecraftSyncHandler
     * @var MinecraftSyncHandler
     */
    private $mcsh;

    /**
     * Liste aller unbekannter User.
     * ['uuid' => 'name']
     * @var array
     */
    private $mcUsers = [];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $this->mcsh = MinecraftSyncHandler::getInstance();

        $unknownUsers = $this->mcsh->getUnknownMinecraftUsers();

        if ($unknownUsers == null) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.error.offline'), 'error');
            exit;
        }
        if (empty($unknownUsers)) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDList'), WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.error.offline'), 'error');
            exit;
        }

        foreach ($unknownUsers as $minecraftID => $uuidArray) {
            foreach ($uuidArray as $uuid => $name) {
                array_push($mcUsers, ['label' => $name, 'value' => $uuid, 'depth' => 0]);
            }
        }
        $fields = [];
        if (MINECRAFT_MAX_IDENTITIES > 1) {
            $fields[] = TextFormField::create('title')
                            ->label('wcf.page.minecraftIDAdd.title')
                            ->description('wcf.page.minecraftIDAdd.title.description')
                            ->maximumLength(30)
                            ->required();
        }
        $fields[] = SingleSelectionFormField::create('minecraftUUID')
                        ->label('wcf.page.minecraftIDAdd.minecraftUUIDfrontend')
                        ->description('wcf.page.minecraftIDAdd.minecraftUUIDfrontend.description')
                        ->options($mcUsers)
                        ->required();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren($fields)
        );
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'mcUsers' => $this->mcUsers
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        $code = bin2hex(\random_bytes(16));

        $title = 'Default'; // Default Name
        if (MINECRAFT_MAX_IDENTITIES > 1 && isset($_POST['title'])) {
            $title = $_POST['title'];
        }

        $ID;
        foreach ($this->mcsh->getUnknownMinecraftUsers() as $minecraftID => $uuidArray) {
            if (array_key_exists($_POST['uuid'], $uuidArray)) {
                $ID = $minecraftID;
                continue;
            }
        }

        $message = WCF::getLanguage()->getDynamicVariable('wcf.page.minecraftIDAdd.code.message', ['username' => WCF::getUser()->username, 'code' => $code]);
        try {
            $this->mcsh->$minecrafts[$ID]->getConnection()->call('tellraw ' . $formData['data']['minecraftUUID'] . ' ' . $message);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                throw $e;
            }
        }
    }
}
