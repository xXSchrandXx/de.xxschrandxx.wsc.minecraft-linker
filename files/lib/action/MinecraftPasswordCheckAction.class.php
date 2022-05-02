<?php

namespace wcf\action;

use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\system\exception\IllegalLinkException;
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
    public function checkModules()
    {
        try {
            parent::checkModules();
        } catch (IllegalLinkException $e) {
            $this->send($e->getMessage(), $e->getCode());
        }
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
            $this->send('Too Many Requests.', 429);
        }
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            parent::execute();
        } catch (IllegalLinkException $e) {
            $this->send($e->getMessage(), $e->getCode());
        }

        if (empty($_POST)) {
            $this->send('Request empty.', 400);
        }
        if (
            !array_key_exists('key', $_POST) ||
            !array_key_exists('uuid', $_POST) ||
            !array_key_exists('password', $_POST)
        ) {
            $this->send('Request missing keys.', 400);
        }
        $key = $_POST['key'];
        $password = $_POST['password'];
        $uuid = $_POST['uuid'];
        if (!is_string($key) || !is_string($password) || !is_string($uuid)) {
            $this->send('Bad Request', 400);
        }
        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $uuid)) {
            $this->send('Invalid UUID format.', 400);
        }
        if (hash_equals(MINECRAFT_LINKER_PASSWORD_KEY, $key)) {
            $this->send('Unauthorized', 401);
        }
        /** @var User */
        $user = null;
        try {
            $user = $this->getUser($uuid);
        } catch (UserInputException $e) {
            $this->send($e->getMessage(), $e->getCode());
        }

        if ($user->checkPassword($password)) {
            $this->send('OK', 200, true);
        } else {
            $this->send('OK', 200);
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

    private function send($status, $statusCode, $valid = false)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo JSON::encode([
            'status' => $status,
            'statusCode' => $statusCode,
            'valid' => $valid
        ]);
        exit;
    }
}
