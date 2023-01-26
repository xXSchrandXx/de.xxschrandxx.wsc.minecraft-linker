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
abstract class AbstractMultipleMinecraftLinkerAction extends AbstractMinecraftAction
{
    /**
     * Weather the request requires a names
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

        // check uuids
        if (!array_key_exists('uuids', $parameters)) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuids\' not set.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
        if (!is_array($parameters['uuids'])) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuids\' no array.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
        // validate uuids
        foreach ($parameters['uuids'] as $uuid => $options) {
            if (!MinecraftLinkerUtil::validUUID($uuid)) {
                unset($parameters['uuids'][$uuid]);
            }
        }
        if (empty($parameters['uuids'])) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuids\' contains no valid uuid.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }

        // check name
        if ($this->ignoreName) {
            return;
        }

        if (empty(array_values($parameters['uuids']))) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuids\' contains no options.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
        foreach ($parameters['uuids'] as $uuid => $options) {
            if (!array_key_exists('name', $options)) {
                unset($parameters['uuids'][$uuid]);
            }
        }
        if (empty($parameters['uuids'])) {
            if (ENABLE_DEBUG_MODE) {
                $response = $this->send('Bad Request. \'uuids\' contains no valid names in options.', 400);
            } else {
                $response = $this->send('Bad Request.', 400);
            }
            return;
        }
    }
}
