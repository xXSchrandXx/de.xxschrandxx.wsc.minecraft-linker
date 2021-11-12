<?php

namespace wcf\system\event\listener;

use wcf\data\user\minecraft\MinecraftList;
use wcf\data\user\UserProfile;

/**
 * Listener for DSGVO export
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Kommerzielle Lizenz (https://hanashi.eu/kommerzielle-lizenz/)
 * @package WoltLabSuite\Core\System\Event\Listener
 */
class MinecraftSyncGdprExportListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute(/** @var UserExportGdprAction $eventObj */$eventObj, $className, $eventName, array &$parameters)
    {
        /** @var UserProfile $user */
        if (!(MINECRAFT_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            return;
        }

        $user = $eventObj->user;

        $minecraftList = new MinecraftList();
        $minecraftList->getConditionBuilder()->add('userID = ?', [$user->userID]);
        $minecraftList->readObjects();

        $dataArr = [];
        foreach ($minecraftList as $minecraft) {
            $dataArr[] = [
                'minecraftUUID' => $minecraft->minecraftUUID,
                'title' => $minecraft->title,
                'createdDate' => $minecraft->createdDate
            ];
        }

        $eventObj->data['de.xxschrandxx.wsc.minecraft-sync'] = $dataArr;
    }
}
