<?php

namespace wcf\system\minecraft;

class BungeeMinecraftLinkerHandler extends AbstractMinecraftLinkerHandler
{
    /**
     * @inheritDoc
     */
    public function getOnlineMinecraftUsers()
    {
        parent::getOnlineMinecraftUsers();
    }

    /**
     * @inheritDoc
     */
    public function sendCode($uuid, $name, $code)
    {
        $command = sprintf(MINECRAFT_COMMAND_BUNGEE_SENDCODE, $_POST['uuid'], $code);
        try {
            $this->minecraft->getConnection()->call($command);
        } catch (MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                throw $e;
            }
            return false;
        }
        return true;
    }
}
