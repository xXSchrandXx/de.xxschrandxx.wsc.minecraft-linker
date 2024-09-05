<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use BadMethodCallException;
use Laminas\Diactoros\Response\JsonResponse;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;
use wcf\system\endpoint\GetRequest;

#[\wcf\http\attribute\DisableXsrfCheck]
#[GetRequest('/xxschrandxx/minecraft/{uuid:[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}}/code')]
final class GetCode extends AbstractMinecraftLinker
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
        // check edit
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$this->uuid]);
        $minecraftUserList->readObjects();
        try {
            /** @var \wcf\data\user\minecraft\MinecraftUser */
            $minecraftUser = $minecraftUserList->getSingleObject();
            if ($minecraftUser !== null) {
                // check linked
                $userToMinecraftUserList = new UserToMinecraftUserList();
                $userToMinecraftUserList->getConditionBuilder()->add('minecraftUserID = ?', [$minecraftUser->getObjectID()]);
                if ($userToMinecraftUserList->countObjects() !== 0) {
                    if (ENABLE_DEBUG_MODE) {
                        $this->response = new JsonResponse(['code' => '']);
                    } else {
                        $this->response = new JsonResponse(['code' => '']);
                    }
                } else {
                    $this->response = new JsonResponse(['code' => $minecraftUser->getCode()]);
                }
                return;
            }
        } catch (BadMethodCallException $e) {
            // should never happen
        }
        $code = bin2hex(\random_bytes(4));
        // create databaseobject
        MinecraftUserEditor::create([
            'minecraftUUID' => $this->uuid,
            'minecraftName' => $this->name,
            'code' => $code
        ]);
        $this->response = new JsonResponse(['code' => $code]);
    }
}
