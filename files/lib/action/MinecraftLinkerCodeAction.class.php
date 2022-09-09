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
    protected string $code;

    /**
     * @inheritDoc
     */
    protected bool $ignoreName = false;

    /**
     * @inheritDoc
     */
    public function readParameters(): ?JsonResponse
    {
        $result = parent::readParameters();

        // check code
        if (!array_key_exists('code', $this->getJSON())) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'code\' not set.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        if (!is_string($this->getData('code'))) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'code\' no string.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        $this->code = $this->getData('code');

        return $result;
    }

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
                    return $this->send('Bad request. UUID already linked.', 400);
                } else {
                    return $this->send('Bad request.', 400);
                }
            } else {
                $editor = new MinecraftUserEditor($minecraftUser);
                $editor->update([
                    'code' => $this->code
                ]);
            }
        } else {
            // create databaseobject
            MinecraftUserEditor::create([
                'minecraftUUID' => $this->uuid,
                'minecraftName' => $this->name,
                'code' => $this->code
            ]);
        }

        // send OK
        return $this->send();
    }
}
