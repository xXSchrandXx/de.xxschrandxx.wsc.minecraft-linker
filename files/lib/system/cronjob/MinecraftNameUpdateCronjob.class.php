<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\minecraft\MinecraftLinkerHandler;

/**
 * MinecraftName update class class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Cronjob
 */
class MinecraftNameUpdateCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {

        if (!MINECRAFT_NAME_ENABLED) {
            return;
        }

        parent::execute($cronjob);

        $savedUsersList = new MinecraftUserList();
        $savedUsersList->readObjects();
        $savedUsers = $savedUsersList->getObjects();

        $mlh = MinecraftLinkerHandler::getInstance();
        $allUsers = $mlh->getOnlineMinecraftUsers();

        foreach ($savedUsers as &$savedUser) {
            $alreadyChecked = false;
            foreach ($allUsers as $minecraftID => $uuidArray) {
                if ($alreadyChecked) {
                    break;
                }
                foreach ($uuidArray as $uuid => $name) {
                    if ($savedUser->minecraftUUID == $uuid) {
                        if ($savedUser->minecraftName != $name) {
                            $editor = new MinecraftUserEditor($savedUser);
                            $editor->update(['minecraftName' => $name]);
                        }
                        $alreadyChecked = true;
                        break;
                    }
                }
            }
        }
    }
}
