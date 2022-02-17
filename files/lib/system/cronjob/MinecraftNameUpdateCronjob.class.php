<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\minecraft\MinecraftLinkerHandler;

class MinecraftNameUpdateCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
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
