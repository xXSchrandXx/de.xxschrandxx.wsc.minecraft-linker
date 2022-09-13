<?php

namespace wcf\action;

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
class MinecraftLinkerCodeAction extends AbstractMinecraftLinkerAction
{
    /**
     * @inheritDoc
     */
    protected bool $ignoreName = false;

    /**
     * @inheritdoc
     */
    public function execute(): ?JsonResponse
    {
        parent::execute();

        // check edit
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$this->uuid]);
        if ($minecraftUserList->countObjects() !== 0) {
            $minecraftUserList->readObjects();
            /** @var \wcf\data\user\minecraft\MinecraftUser */
            $minecraftUser = $minecraftUserList->getSingleObject();
            // check linked
            $userToMinecraftUserList = new UserToMinecraftUserList();
            $userToMinecraftUserList->setObjectIDs([$minecraftUser->getObjectID()]);
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
        $code = bin2hex(\random_bytes(4));
        // create databaseobject
        MinecraftUserEditor::create([
            'minecraftUUID' => $this->uuid,
            'minecraftName' => $this->name,
            'code' => $code
        ]);
        return $this->send('OK', 200, ['code' => $code]);
    }
}
