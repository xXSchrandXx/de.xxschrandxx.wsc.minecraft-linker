<?php

namespace wcf\system\event\listener;

use wcf\data\user\teamspeak\TeamspeakList;
use wcf\system\teamspeak\TeamSpeakSyncHandler;
use wcf\data\user\UserList;

/**
 * Listener um den Sync mit TeamSpeak zu garantieren
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Kommerzielle Lizenz (https://hanashi.eu/kommerzielle-lizenz/)
 * @package WoltLabSuite\Core\System\Event\Listener
 */
class TeamSpeakSyncListener implements IParameterizedEventListener
{
    private $teamspeakIDs = [];

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!(HANASHI_TEAMSPEAK_ENABLED && HANASHI_TEAMSPEAK_SYNC_IDENTITY)) {
            return;
        }

        $this->$eventName($eventObj);
    }

    protected function initializeAction($eventObj)
    {
        if ($eventObj->getActionName() == 'delete') {
            $users = new UserList();
            $users->setObjectIDs($eventObj->getObjectIDs());
            $users->readObjects();

            if (!count($users)) {
                return;
            }

            $userList = $users->getObjects();

            $teamspeakList = new TeamspeakList();
            $teamspeakList->getConditionBuilder()->add('userID IN (?)', [$users->getObjectIDs()]);
            $teamspeakList->readObjects();

            if (!count($teamspeakList)) {
                return;
            }

            foreach ($teamspeakList as $teamspeak) {
                if (empty($userList[$teamspeak->userID])) {
                    continue;
                }

                TeamSpeakSyncHandler::getInstance()->deleteUser($userList[$teamspeak->userID], $teamspeak);
            }
        }
    }

    protected function finalizeAction($eventObj)
    {
        $parameter = $eventObj->getParameters();
        $actionName = $eventObj->getActionName();

        if (!in_array($actionName, ['update', 'addToGroups', 'removeFromGroups', 'ban', 'unban'])) {
            return;
        }

        $users = new UserList();
        $users->setObjectIDs($eventObj->getObjectIDs());
        $users->readObjects();

        if (!count($users)) {
            return;
        }

        $teamspeakList = new TeamspeakList();
        $teamspeakList->getConditionBuilder()->add('userID IN (?)', [$users->getObjectIDs()]);
        $teamspeakList->readObjects();

        $teamspeakIDs = [];
        foreach ($teamspeakList as $teamspeakID) {
            $teamspeakIDs[$teamspeakID->userID][] = $teamspeakID;
        }
        $this->teamspeakIDs = $teamspeakIDs;

        if (!count($this->teamspeakIDs)) {
            return;
        }

        foreach ($users as $user) {
            if (!isset($this->teamspeakIDs[$user->userID])) {
                continue;
            }

            if ($actionName == 'update') {
                if (HANASHI_TEAMSPEAK_SYNC_DESCRIPTION) {
                    foreach ($this->teamspeakIDs[$user->userID] as $teamspeakID) {
                        TeamSpeakSyncHandler::getInstance()->syncDescription($user, $teamspeakID);
                    }
                }
                if (HANASHI_TEAMSPEAK_SYNC_BANS) {
                    $this->banUser($eventObj);
                }
            } elseif ($actionName == 'addToGroups' || $actionName == 'removeFromGroups') {
                TeamSpeakSyncHandler::getInstance()->syncUser($user, $this->teamspeakIDs[$user->userID]);
            } elseif (HANASHI_TEAMSPEAK_SYNC_BANS && $actionName == 'ban') {
                TeamSpeakSyncHandler::getInstance()->ban($user, $parameter['banReason'], $parameter['banExpires'], $this->teamspeakIDs[$user->userID]);
            } elseif (HANASHI_TEAMSPEAK_SYNC_BANS && $actionName == 'unban') {
                TeamSpeakSyncHandler::getInstance()->unban($user, $this->teamspeakIDs[$user->userID]);
            }
        }
    }

    protected function banUser($eventObj)
    {
        $parameter = $eventObj->getParameters();
        $objects = $eventObj->getObjects();
        foreach ($objects as $object) {
            $user = $object->getDecoratedObject();
            if (!isset($this->teamspeakIDs[$user->userID])) {
                continue;
            }
            if (!isset($parameter['data']['banned'])) {
                continue;
            }
            if ($user->banned == $parameter['data']['banned']) {
                continue;
            }

            if ($parameter['data']['banned']) {
                TeamSpeakSyncHandler::getInstance()->ban($user, $parameter['data']['banReason'], $parameter['data']['banExpires'], $this->teamspeakIDs[$user->userID]);
            } else {
                TeamSpeakSyncHandler::getInstance()->unban($user, $this->teamspeakIDs[$user->userID]);
            }
        }
    }
}
