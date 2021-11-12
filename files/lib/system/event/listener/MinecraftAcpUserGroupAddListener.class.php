<?php

namespace wcf\system\event\listener;

use wcf\acp\form\UserGroupEditForm;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\exception\UserInputException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\JSON;

/**
 * Listener for editing a user group on acp
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Kommerzielle Lizenz (https://hanashi.eu/kommerzielle-lizenz/)
 * @package WoltLabSuite\Core\System\Event\Listener
 */
class MinecraftAcpUserGroupAddListener implements IParameterizedEventListener
{
    /**
     * Liste der TeamSpeak Server Gruppen IDs
     *
     * @var array
     */
    protected $teamSpeakServerGroupIDs = [];

    /**
     * List der TeamSpeak Channel IDs
     *
     * @var array
     */
    protected $teamSpeakChannelIDs = [];

    /**
     * ID der Channelgruppe die den Nutzer zugewiesen werden soll
     *
     * @var int
     */
    protected $teamSpeakChannelGroupID = [];

    /**
     * ID der Channelgruppe die den Nutzer zugewiesen werden soll wenn die Verbindung getrennt wird
     *
     * @var int
     */
    protected $teamSpeakGuestChannelGroupID = [];

    /**
     * Sync-Richtung
     *
     * @var int
     */
    protected $teamSpeakSyncDirection = [];

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!(MINECRAFT_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.minecraftSynchronisation.canManage')) {
            return;
        }

        $this->$eventName($eventObj);
    }

    /**
     * @see AbstractForm::readFormparameters()
     */
    public function readFormParameters()
    {
        if (isset($_POST['teamSpeakServerGroupIDs'])) {
            $this->teamSpeakServerGroupIDs = $_POST['teamSpeakServerGroupIDs'];
        }
        if (isset($_POST['teamSpeakChannelIDs'])) {
            $this->teamSpeakChannelIDs = $_POST['teamSpeakChannelIDs'];
        }
        if (isset($_POST['teamSpeakChannelGroupID'])) {
            $this->teamSpeakChannelGroupID = $_POST['teamSpeakChannelGroupID'];
        }
        if (isset($_POST['teamSpeakGuestChannelGroupID'])) {
            $this->teamSpeakGuestChannelGroupID = $_POST['teamSpeakGuestChannelGroupID'];
        }
        if (isset($_POST['teamSpeakSyncDirection'])) {
            $this->teamSpeakSyncDirection = $_POST['teamSpeakSyncDirection'];
        }
    }

    /**
     * @see AbstractForm::validate()
     */
    public function validate($eventObj)
    {
        foreach (TeamSpeakSyncHandler::getInstance()->getTeamspeaks() as $teamspeak) {
            if (!empty($this->teamSpeakChannelGroupID[$teamspeak->teamspeakID])) {
                if (empty($this->teamSpeakChannelIDs[$teamspeak->teamspeakID])) {
                    throw new UserInputException('teamSpeakChannelIDs-' . $teamspeak->teamspeakID, 'noChannelsSelected');
                }
                if (empty($this->teamSpeakGuestChannelGroupID[$teamspeak->teamspeakID])) {
                    throw new UserInputException('teamSpeakGuestChannelGroupID-' . $teamspeak->teamspeakID, 'noGuestChannelGroupSelected');
                }
            } elseif (!empty($this->teamSpeakGuestChannelGroupID[$teamspeak->teamspeakID])) {
                if (empty($this->teamSpeakChannelIDs[$teamspeak->teamspeakID])) {
                    throw new UserInputException('teamSpeakChannelIDs-' . $teamspeak->teamspeakID, 'noChannelsSelected');
                }
                if (empty($this->teamSpeakChannelGroupID[$teamspeak->teamspeakID])) {
                    throw new UserInputException('teamSpeakChannelGroupID-' . $teamspeak->teamspeakID, 'noChannelGroupSelected');
                }
            }
            if (isset($this->teamSpeakSyncDirection[$teamspeak->teamspeakID])) {
                if (!in_array($this->teamSpeakSyncDirection[$teamspeak->teamspeakID], ['sync', 'reverseSync'])) {
                    throw new UserInputException('teamSpeakSyncDirection-' . $teamspeak->teamspeakID, 'invalid');
                }
                if ($this->teamSpeakSyncDirection[$teamspeak->teamspeakID] == 'reverseSync' && !empty($eventObj->group) && $eventObj->group->getDecoratedObject()->isAdminGroup()) {
                    throw new UserInputException('teamSpeakSyncDirection-' . $teamspeak->teamspeakID, 'adminNotAllowed');
                }
            }
        }
    }

    /**
     * @see AbstractForm::save()
     */
    public function save($eventObj)
    {
        $eventObj->additionalFields = array_merge($eventObj->additionalFields, [
            'teamSpeakServerGroupIDs' => JSON::encode($this->teamSpeakServerGroupIDs),
            'teamSpeakChannelIDs' => JSON::encode($this->teamSpeakChannelIDs),
            'teamSpeakChannelGroupID' => JSON::encode($this->teamSpeakChannelGroupID),
            'teamSpeakGuestChannelGroupID' => JSON::encode($this->teamSpeakGuestChannelGroupID),
            'teamSpeakSyncDirection' => JSON::encode($this->teamSpeakSyncDirection)
        ]);

        if (HANASHI_TEAMSPEAK_ENABLE_BACKGROUND_JOB) {
            BackgroundQueueHandler::getInstance()->enqueueIn(new TeamSpeakGlobalSyncBackgroundJob());
        }

        // // reset values
        if (!($eventObj instanceof UserGroupEditForm)) {
            $this->teamSpeakServerGroupIDs = [];
            $this->teamSpeakChannelIDs = [];
            $this->teamSpeakChannelGroupID = [];
            $this->teamSpeakGuestChannelGroupID = [];
            $this->teamSpeakSyncDirection = [];
        }
    }

    /**
     * @see AbstractForm::assignVariables()
     */
    public function assignVariables($eventObj)
    {
        if (empty($_POST) && $eventObj instanceof UserGroupEditForm) {
            try {
                $this->teamSpeakServerGroupIDs = JSON::decode($eventObj->group->teamSpeakServerGroupIDs);
                $this->teamSpeakChannelIDs = JSON::decode($eventObj->group->teamSpeakChannelIDs);
                $this->teamSpeakChannelGroupID = JSON::decode($eventObj->group->teamSpeakChannelGroupID);
                $this->teamSpeakGuestChannelGroupID = JSON::decode($eventObj->group->teamSpeakGuestChannelGroupID);
                $this->teamSpeakSyncDirection = JSON::decode($eventObj->group->teamSpeakSyncDirection);
            } catch (SystemException $e) {
                // do nothing
            }
        }

        $teamspeak3ServerGroups = TeamSpeakSyncHandler::getInstance()->getServerGroups();
        $teamspeak3Channels = $this->getChannelsOptions(TeamSpeakSyncHandler::getInstance()->getChannels());
        $teamspeak3ChannelGroups = TeamSpeakSyncHandler::getInstance()->getChannelGroups();

        // assign variables
        WCF::getTPL()->assign([
            'teamspeaks' => TeamSpeakSyncHandler::getInstance()->getTeamspeaks(),
            'teamspeak3ServerGroupsGrouped' => $teamspeak3ServerGroups,
            'teamspeak3Channels' => $teamspeak3Channels,
            'teamspeak3ChannelGroups' => $teamspeak3ChannelGroups,
            'teamSpeakServerGroupIDs' => $this->teamSpeakServerGroupIDs,
            'teamSpeakChannelIDs' => $this->teamSpeakChannelIDs,
            'teamSpeakChannelGroupID' => $this->teamSpeakChannelGroupID,
            'teamSpeakGuestChannelGroupID' => $this->teamSpeakGuestChannelGroupID,
            'teamSpeakSyncDirection' => $this->teamSpeakSyncDirection
        ]);
    }

    /**
     * Methode um den Channelbaum in ein sinnvolles Format zu holen
     *
     * @param   array   $teamspeak3Channels     Liste der TeamSpeak-Channel
     * @return  array
     */
    private function getChannelsOptions($teamspeakChannelsGrouped)
    {
        $channelsGrouped = [];
        foreach ($teamspeakChannelsGrouped as $teamspeakID => $teamspeakChannels) {
            $channels = [];
            foreach ($teamspeakChannels as $teamspeakChannel) {
                $channels = array_merge($channels, $this->getChannelOption($teamspeakID, $teamspeakChannel));
            }
            $channelsGrouped[$teamspeakID] = $channels;
        }

        return $channelsGrouped;
    }

    /**
     * Methode um einen einzelnen Channel sinnvoll zu formatieren
     *
     * @param   array   $teamspeakChannel   Objekt des Teamspeak-Channels
     * @param   int     $padding            Anzahl der voranstehenden Leerzeichen (ursprünglich paddings; waren aber inkompatibel mit Safari)
     * @return  array
     */
    private function getChannelOption($teamspeakID, $teamspeakChannel, $padding = 0)
    {
        $channels = [];
        $checked = '';
        if (!empty($this->teamSpeakChannelIDs[$teamspeakID]) && in_array($teamspeakChannel['cid'], $this->teamSpeakChannelIDs[$teamspeakID])) {
            $checked = ' checked';
        }
        $spaces = '';
        for ($i = 0; $i < $padding; $i++) {
            $spaces .= '&nbsp';
        }
        $channels[] = $spaces . '<label><input type="checkbox" name="teamSpeakChannelIDs[' . $teamspeakID . '][]" value="' . $teamspeakChannel['cid'] . '"' . $checked . '> ' . $teamspeakChannel['channel_name'] . '</label>';
        foreach ($teamspeakChannel['childs'] as $teamspeakChannelChild) {
            $channels = array_merge($channels, $this->getChannelOption($teamspeakID, $teamspeakChannelChild, $padding + 3));
        }

        return $channels;
    }
}
