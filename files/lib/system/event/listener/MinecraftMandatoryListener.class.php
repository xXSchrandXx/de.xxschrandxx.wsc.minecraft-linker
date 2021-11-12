<?php

namespace wcf\system\event\listener;

use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\system\request\LinkHandler;

/**
 * Listener um Minecraft-Sync verpflichtend zu machen
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Kommerzielle Lizenz (https://hanashi.eu/kommerzielle-lizenz/)
 * @package WoltLabSuite\Core\System\Event\Listener
 */
class MinecraftMandatoryListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!(MINECRAFT_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            return;
        }

        if (
            in_array($className, [
                'wcf\page\MinecraftIDListPage',
                'wcf\form\MinecraftIDAddForm',
                'wcf\form\MinecraftIDCheckForm',
                'wcf\form\AccountManagementForm',
                'wcf\form\HaTwoStepVerificationForm'
            ])
        ) {
            return;
        }

        $user = WCF::getUser();

        if (!$user->userID || $user->minecraftUUIDS > 0) {
            return;
        }
        if (!MINECRAFT_ENABLE_ACTIVE_USER && $user->activationCode && REGISTER_ACTIVATION_METHOD == 1) {
            return;
        }

        if (WCF::getSession()->getPermission('user.minecraftSynchronisation.mandatory')) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('MinecraftIDAdd', ['forceFrontend' => true]), WCF::getLanguage()->getDynamicVariable('wcf.redirect.minecraft.mandatory'), 5, 'warning');
            exit;
        }
    }
}
