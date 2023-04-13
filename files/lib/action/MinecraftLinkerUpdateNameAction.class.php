<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;

/**
 * MinecraftLinkerUpdateName action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
#[\wcf\http\attribute\DisableXsrfCheck]
class MinecraftLinkerUpdateNameAction extends AbstractMultipleMinecraftLinkerAction
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_LINKER_ENABLED'];

    /**
     * @inheritDoc
     */
    public bool $ignoreName = false;

    /**
     * @inheritDoc
     */
    public $availableMinecraftIDs = MINECRAFT_LINKER_IDENTITY;

    /**
     * @inheritdoc
     */
    public function execute($parameters): JsonResponse
    {
        // read minecraftUsers
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID IN (?)', [array_keys($parameters['uuids'])]);
        if ($minecraftUserList->countObjects() === 0) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Unknown UUIDs', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        $minecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\MinecraftUser[] */
        $minecraftUsers = $minecraftUserList->getObjects();

        foreach ($minecraftUsers as $minecraftUser) {
            if (!array_key_exists($minecraftUser->getMinecraftUUID(), $parameters['uuids'])) {
                // Would never happen
                continue;
            }
            if (empty($parameters['uuids'][$minecraftUser->getMinecraftUUID()])) {
                // Would never happen
                continue;
            }
            if (!array_key_exists('name', $parameters['uuids'][$minecraftUser->getMinecraftUUID()])) {
                continue;
            }
            if (empty($parameters['uuids'][$minecraftUser->getMinecraftUUID()]['name'])) {
                continue;
            }
            if ($minecraftUser->getMinecraftName() === $parameters['uuids'][$minecraftUser->getMinecraftUUID()]['name']) {
                continue;
            }
            $minecraftUserEditor = new MinecraftUserEditor($minecraftUser);
            $minecraftUserEditor->update([
                'minecraftName' => $parameters['uuids'][$minecraftUser->getMinecraftUUID()]['name']
            ]);
        }

        return $this->send();
    }
}
