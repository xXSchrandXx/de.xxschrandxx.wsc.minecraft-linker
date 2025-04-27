<?php

namespace minecraft\system\endpoint\controller\xxschrandxx\minecraft\linker;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use minecraft\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\endpoint\GetRequest;
use minecraft\util\MinecraftLinkerUtil;

#[GetRequest('/xxschrandxx/minecraft/unlinked')]
final class GetUnlinked extends AbstractMinecraft
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
            $minecraftUserList = MinecraftLinkerUtil::getUnlinkedMinecraftUser();
            $minecraftUserList->readObjects();
            /** @var \wcf\data\user\minecraft\MinecraftUser[] */
            $minecraftUsers = $minecraftUserList->getObjects();
        } catch (Exception $e) {
            // Exception handled with empty check
        }
        if (empty($minecraftUsers)) {
            $this->response =  new JsonResponse(['uuids' => []]);
            return;
        }

        $uuids = [];
        foreach ($minecraftUsers as $minecraftUser) {
            \array_push($uuids, $minecraftUser->getMinecraftUUID());
        }

        $this->response = new JsonResponse(['uuids' => $uuids]);
    }
}