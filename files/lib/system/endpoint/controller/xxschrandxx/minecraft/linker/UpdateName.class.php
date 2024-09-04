<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\http\Helper;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\UserInputException;

#[PostRequest('/xxschrandxx/minecraft/{id:\d+}/{uuid:[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}}/name')]
final class UpdateName extends AbstractMinecraft
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
    public function execute(): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($this->request, UpdateNameParameters::class);

        // read minecraftUsers
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID IN (?)', [array_keys($parameters->uuids)]);
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
            if (!array_key_exists($minecraftUser->getMinecraftUUID(), $parameters->uuids)) {
                // Would never happen
                continue;
            }
            if (empty($parameters->uuids[$minecraftUser->getMinecraftUUID()])) {
                // Would never happen
                continue;
            }
            if (!array_key_exists('name', $parameters->uuids[$minecraftUser->getMinecraftUUID()])) {
                continue;
            }
            if (empty($parameters->uuids[$minecraftUser->getMinecraftUUID()]['name'])) {
                continue;
            }
            if ($minecraftUser->getMinecraftName() === $parameters->uuids[$minecraftUser->getMinecraftUUID()]['name']) {
                continue;
            }
            $minecraftUserEditor = new MinecraftUserEditor($minecraftUser);
            $minecraftUserEditor->update([
                'minecraftName' => $parameters->uuids[$minecraftUser->getMinecraftUUID()]['name']
            ]);
        }

        return new EmptyResponse(200);
    }
}


/** @internal */
class UpdateNameParameters
{
    public function __construct(
        /** @var non-empty-array */
        public readonly array $uuids
    ) {
    }
}
