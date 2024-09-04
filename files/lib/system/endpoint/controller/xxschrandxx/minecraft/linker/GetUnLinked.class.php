<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\endpoint\GetRequest;
use wcf\util\MinecraftLinkerUtil;

#[GetRequest('/xxschrandxx/minecraft/{id:\d+}/unlinked')]
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
    public function execute(): ResponseInterface
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
            return new JsonResponse(['uuids' => []]);
        }

        $uuids = [];
        foreach ($minecraftUsers as $minecraftUser) {
            \array_push($uuids, $minecraftUser->getMinecraftUUID());
        }

        return new JsonResponse(['uuids' => $uuids]);
    }
}