<?php

namespace wcf\action;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use wcf\util\MinecraftLinkerUtil;

/**
 * AbstractMinecraftLinker action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class MinecraftLinkerGetLinkedAction extends AbstractMinecraftGETAction
{
    /**
     * @inheritDoc
     */
    protected $availableMinecraftIDs = MINECRAFT_LINKER_IDENTITY;

    public function execute(): ?JsonResponse
    {
        parent::execute();

        $minecraftUsers = [];
        try {
            $minecraftUserList = MinecraftLinkerUtil::getLinkedMinecraftUser();
            $minecraftUserList->readObjects();
            /** @var \wcf\data\user\minecraft\MinecraftUser[] */
            $minecraftUsers = $minecraftUserList->getObjects();
        } catch (Exception $e) {
            // Exception handled with empty check
        }
        if (empty($minecraftUsers)) {
            return $this->send('OK', 200, [
                'uuids' => []
            ]);
        }

        $uuids = [];
        foreach ($minecraftUsers as $minecraftUser) {
            array_push($uuids, $minecraftUser->getMinecraftUUID());
        }

        return $this->send('OK', 200, [
            'uuids' => $uuids
        ]);
    }
}
