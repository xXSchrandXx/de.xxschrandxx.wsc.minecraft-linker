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
     * Weather the request requires a name
     * @var bool
     */
    public bool $ignoreName = true;

    /**
     * @inheritDoc
     */
    public function validateParameters($parameters, &$response): void
    {
        parent::validateParameters($parameters, $response);
        if ($response instanceof JsonResponse) {
            return;
        }

        // check uuid
        if (!array_key_exists('uuid', $parameters)) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuid\' not set.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
        if (!is_string($parameters['uuid'])) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuid\' no string.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
        if (!MinecraftLinkerUtil::validUUID($parameters['uuid'])) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuid\' is no valid UUID.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }

        // check name
        if ($this->ignoreName) {
            return;
        }

        if (!array_key_exists('name', $parameters)) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'name\' not set.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
        if (!is_string($parameters['name'])) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'name\' no string.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
    }
}
