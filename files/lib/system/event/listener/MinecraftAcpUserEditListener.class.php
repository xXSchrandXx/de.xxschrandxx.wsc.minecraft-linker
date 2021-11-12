<?php

namespace wcf\system\event\listener;

use wcf\data\user\minecraft\MinecraftList;
use wcf\system\WCF;

/**
 * Übersicht der Identitäten beim Editieren eines Benutzers
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Kommerzielle Lizenz (https://hanashi.eu/kommerzielle-lizenz/)
 * @package WoltLabSuite\Core\System\Event\Listener
 */
class MinecraftAcpUserEditListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $this->$eventName($eventObj);
    }

    /**
     * @see AbstractPage::assignVariables()
     */
    public function assignVariables($eventObj)
    {
        if (!(MINECRAFT_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.minecraftSynchronisation.canManage')) {
            return;
        }

        $minecraftList = new MinecraftList();
        $minecraftList->getConditionBuilder()->add('userID = ?', [$eventObj->userID]);
        $minecraftList->readObjects();

        WCF::getTPL()->assign([
            'minecraftList' => $minecraftList
        ]);
    }
}
