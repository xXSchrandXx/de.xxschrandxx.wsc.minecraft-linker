<?php

namespace wcf\action;

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
    public function readParameters()
    {
        parent::readParameters();

        // check code
        if (!array_key_exists('code', $_POST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'code\' not set.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        if (!is_string($_POST['code'])) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'code\' no string.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        $this->code = $_POST['code'];
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        parent::execute();

        // check edit
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$this->uuid]);
        if ($minecraftUserList->countObjects() !== 0) {
            $minecraftUserList->readObjects();
            $minecraftUser = $minecraftUserList->getSingleObject();
            // check linked
            $userToMinecraftUserList = new UserToMinecraftUserList();
            $userToMinecraftUserList->setObjectIDs([$minecraftUser->minecraftUserID]);
            if ($userToMinecraftUserList->countObjects() !== 0) {
                if (ENABLE_DEBUG_MODE) {
                    $this->send('Bad request. UUID already linked.', 401);
                } else {
                    $this->send('Bad request.', 401);
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
