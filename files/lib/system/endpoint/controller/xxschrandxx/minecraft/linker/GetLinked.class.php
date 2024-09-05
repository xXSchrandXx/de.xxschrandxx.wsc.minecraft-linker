<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\endpoint\GetRequest;
use wcf\util\MinecraftLinkerUtil;

#[GetRequest('/xxschrandxx/minecraft/linked')]
final class getLinked extends AbstractMinecraft
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_LINKER_ENABLED'];

    /**
     * @inheritDoc
     */
    public $availableMinecraftIDs = MINECRAFT_LINKER_IDENTITY;

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
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
            $this->response = new JsonResponse(['uuids' => []]);
            return;
        }

        $uuids = [];
        foreach ($minecraftUsers as $minecraftUser) {
            \array_push($uuids, $minecraftUser->getMinecraftUUID());
        }

        $this->response = new JsonResponse(['uuids' => $uuids]);
    }
}