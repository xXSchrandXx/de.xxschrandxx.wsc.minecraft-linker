<?php

namespace wcf\action;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\system\exception\UserInputException;
use wcf\system\flood\FloodControl;
use wcf\util\JSON;

/**
 * MinecraftPasswordCheck action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class MinecraftPasswordCheckAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_LINKER_PASSWORD_ENABLED','MINECRAFT_LINKER_PASSWORD_KEY'];

    private string $d = 'de.xxschrandxx.wsc.minecraftLinker.passwordcheck';

    /**
     * @inheritDoc
     */
    public function __run()
    {
        if (MINECRAFT_LINKER_FLOODGATE_MAXREQUESTS > 0) {
            FloodControl::getInstance()->registerContent($this->d);
        }
        parent::__run();
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions()
    {
        parent::checkPermissions();

        if (MINECRAFT_LINKER_FLOODGATE_MAXREQUESTS <= 0) {
            return;
        }
        $secs = MINECRAFT_LINKER_FLOODGATE_RESETTIME * 60;
        $time = \ceil(TIME_NOW / $secs) * $secs;
        $data = FloodControl::getInstance()->countContent($this->d, new \DateInterval('PT' . MINECRAFT_LINKER_FLOODGATE_RESETTIME . 'M'), $time);
        if ($data['count'] > MINECRAFT_LINKER_FLOODGATE_MAXREQUESTS) {
            echo JSON::encode([
                'status' => 'Too Many Requests',
                'statusCode' => 429,
                'valid' => false
            ]);
            exit;
        }
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        parent::execute();

        if (empty($_POST)) {
            echo JSON::encode([
                'status' => 'request empty',
                'statusCode' => 400,
                'valid' => false
            ]);
            return;
        }
        if (
            !array_key_exists('key', $_POST) ||
            !array_key_exists('uuid', $_POST) ||
            !array_key_exists('password', $_POST)
        ) {
            echo JSON::encode([
                'status' => 'request missing keys',
                'statusCode' => 400,
                'valid' => false
            ]);
            return;
        }
        $key = $_POST['key'];
        $password = $_POST['password'];
        $uuid = $_POST['uuid'];
        // TODO Check weather post elemts are valid
        if ($key !== MINECRAFT_LINKER_PASSWORD_KEY) {
            echo JSON::encode([
                'status' => 'Unauthorized',
                'statusCode' => 401,
                'valid' => false
            ]);
            return;
        }
        /** @var User */
        $user = null;
        try {
            $user = $this->getUser($uuid);
        } catch (UserInputException $e) {
            echo JSON::encode([
                'status' => $e->getMessage(),
                'statusCode' => $e->getCode(),
                'valid' => false
            ]);
            return;
        }

        if ($user->checkPassword($password)) {
            echo JSON::encode([
                'status' => 'OK',
                'statusCode' => 200,
                'valid' => true
            ]);
        } else {
            echo JSON::encode([
                'status' => 'OK',
                'statusCode' => 200,
                'valid' => false
            ]);
        }
    }

    private function getUser(string $uuid): User
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$uuid]);
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();
        if (empty($minecraftUsers)) {
            throw new UserInputException();
        }
        return new User(array_values($minecraftUsers)[0]->userID);
    }
}
