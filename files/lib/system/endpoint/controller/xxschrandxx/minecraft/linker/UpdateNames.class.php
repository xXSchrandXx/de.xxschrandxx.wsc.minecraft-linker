<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\UserInputException;

#[PostRequest('/xxschrandxx/minecraft/names')]
final class UpdateNames extends AbstractMultipleMinecraftLinker
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
    public bool $ignoreName = false;

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        // read minecraftUsers
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID IN (?)', [array_keys($this->uuids)]);
        if ($minecraftUserList->countObjects() === 0) {
            if (ENABLE_DEBUG_MODE) {
                throw new UserInputException('uuids', 'Unknown uuids');
            } else {
                throw new UserInputException('uuids');
            }
        }
        $minecraftUserList->readObjects();
        /** @var \wcf\data\user\minecraft\MinecraftUser[] */
        $minecraftUsers = $minecraftUserList->getObjects();

        foreach ($minecraftUsers as $minecraftUser) {
            if (!array_key_exists($minecraftUser->getMinecraftUUID(), $this->uuids)) {
                // Would never happen
                continue;
            }
            if (empty($this->uuids[$minecraftUser->getMinecraftUUID()])) {
                // Would never happen
                continue;
            }
            if (!array_key_exists('name', $this->uuids[$minecraftUser->getMinecraftUUID()])) {
                continue;
            }
            if (empty($this->uuids[$minecraftUser->getMinecraftUUID()]['name'])) {
                continue;
            }
            if ($minecraftUser->getMinecraftName() === $this->uuids[$minecraftUser->getMinecraftUUID()]['name']) {
                continue;
            }
            $minecraftUserEditor = new MinecraftUserEditor($minecraftUser);
            $minecraftUserEditor->update([
                'minecraftName' => $this->uuids[$minecraftUser->getMinecraftUUID()]['name']
            ]);
        }

        $this->response = new EmptyResponse(200);
    }
}
