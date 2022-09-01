<?php

namespace wcf\action;

use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;

/**
 * MinecraftLinkerCode action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
abstract class MinecraftLinkerCodeAction extends AbstractMinecraftAction
{
    /**
     * @inheritDoc
     */
    protected bool $setName = true;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        parent::execute();

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
        $code = $_POST['code'];

        // check duplicate entry
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$this->uuid]);
        if ($minecraftUserList->countObjects() !== 0) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Duplicated \'uuid\'.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }

        // create databaseobject
        MinecraftUserEditor::create([
            'minecraftUUID' => $this->uuid,
            'minecraftName' => $this->name,
            'code' => $code
        ]);

        // send OK
        $this->send();
    }
}
