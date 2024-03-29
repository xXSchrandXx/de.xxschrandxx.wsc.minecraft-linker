<?php

namespace wcf\action;

use BadMethodCallException;
use Laminas\Diactoros\Response\JsonResponse;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUserList;

/**
 * MinecraftLinkerCode action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
#[\wcf\http\attribute\DisableXsrfCheck]
class MinecraftLinkerCodeAction extends AbstractMinecraftLinkerAction
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
        // check edit
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$parameters['uuid']]);
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
                        return $this->send('OK UUID already linked.', 200, ['code' => '']);
                    } else {
                        return $this->send('OK', 200, ['code' => '']);
                    }
                } else {
                    return $this->send('OK', 200, ['code' => $minecraftUser->getCode()]);
                }
            }
        } catch (BadMethodCallException $e) {
        }
        $code = bin2hex(\random_bytes(4));
        // create databaseobject
        MinecraftUserEditor::create([
            'minecraftUUID' => $parameters['uuid'],
            'minecraftName' => $parameters['name'],
            'code' => $code
        ]);
        return $this->send('OK', 200, ['code' => $code]);
    }
}
