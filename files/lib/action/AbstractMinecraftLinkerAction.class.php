<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\util\MinecraftLinkerUtil;

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
     * Minecraft uuid of the request
     * @var string
     */
    protected string $uuid;

    /**
     * Weather the request requires a name
     * @var bool
     */
    protected bool $ignoreName = true;

    /**
     * Minecraft name of the request
     * @var string
     */
    protected string $name;

    /**
     * @inheritDoc
     */
    public function readParameters(): ?JsonResponse
    {
        $result = parent::readParameters();

        if ($result !== null) {
            return $result;
        }

        // check uuid
        if (!array_key_exists('uuid', $this->getJSON())) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'uuid\' not set.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        if (!is_string($this->getData('uuid'))) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'uuid\' no string.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        if (!MinecraftLinkerUtil::validUUID($this->getData('uuid'))) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'uuid\' is no valid UUID.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        $this->uuid = $this->getJSON()['uuid'];

        // check name
        if ($this->ignoreName) {
            return $result;
        }
        if (!array_key_exists('name', $this->getJSON())) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'name\' not set.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        if (!is_string($this->getData('name'))) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'name\' no string.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        $this->name = $this->getData('name');

        return $result;
    }
}
