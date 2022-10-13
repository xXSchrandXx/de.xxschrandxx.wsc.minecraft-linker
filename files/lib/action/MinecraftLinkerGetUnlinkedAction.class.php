<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\util\MinecraftLinkerUtil;

/**
 * AbstractMinecraftLinker action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class MinecraftLinkerGetUnlinkedAction extends AbstractMinecraftGETAction
{
    /**
     * @inheritDoc
     */
    protected $availableMinecraftIDs = MINECRAFT_LINKER_IDENTITY;

    public function execute(): ?JsonResponse
    {
        parent::execute();

        $minecraftUserList = MinecraftLinkerUtil::getUnlinkedMinecraftUser();
        $minecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\MinecraftUser[] */
        $minecraftUsers = $minecraftUserList->getObjects();
        if (empty($minecraftUsers)) {
            return $this->send('OK', 200, [
                'uuids' => []
            ]);
        }

        $uuids = [];
        foreach ($minecraftUsers as $minecraftUser) {
            $uuids[$minecraftUser->getMinecraftUUID()] = $minecraftUser->getCode();
        }

        return $this->send('OK', 200, [
            'uuids' => $uuids
        ]);
    }
}
