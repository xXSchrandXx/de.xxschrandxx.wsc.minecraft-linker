<?php

namespace wcf\action;

use wcf\system\util\MojangUtil;
use wcf\util\StringUtil;

/**
 * AbstractMinecraftLinker action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
abstract class AbstractMinecraftLinkerAction extends AbstractMinecraftAction
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_LINKER_ENABLED', 'MINECRAFT_LINKER_IDENTITY'];

    /**
     * Minecraft uuid of the request
     * @var string
     */
    protected string $uuid;

    /**
     * Weather the request requires a name
     * @var bool
     */
    protected bool $setName = false;

    /**
     * Minecraft name of the request
     * @var string
     */
    protected string $name;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        parent::execute();

        // check if minecraftLinker for server enabled
        /** @var string[] */
        if (!in_array($this->minecraft->minecraftID, explode("\n", StringUtil::unifyNewlines(MINECRAFT_LINKER_IDENTITY)))) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Not \'minecraft\' with given id.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }

        // check uuid
        if (!array_key_exists('uuid', $_POST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'uuid\' not set.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        if (!is_string($_POST['uuid'])) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'uuid\' no string.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        if (!preg_match('/' . MojangUtil::UUID_PATTERN . '/', $_POST['uuid'])) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'uuid\' is no valid UUID.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        $this->uuid = $_POST['uuid'];

        // check name
        if ($this->setName) {
            return;
        }
        if (!array_key_exists('name', $_POST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'name\' not set.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        if (!is_string($_POST['name'])) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'name\' no string.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        $this->name = $_POST['name'];
    }
}
