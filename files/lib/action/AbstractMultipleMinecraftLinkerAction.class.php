<?php

namespace wcf\action;

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
     * Minecraft uuids of the request
     * @var array
     */
    protected array $uuids;

    /**
     * Weather the request requires a names
     * @var bool
     */
    protected bool $ignoreName = true;

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        // check uuids
        if (!array_key_exists('uuids', $this->getJSON())) {
            if (ENABLE_DEBUG_MODE) {
                throw $this->exception('Bad Request. \'uuids\' not set.', 400);
            } else {
                throw $this->exception('Bad Request.', 400);
            }
        }
        if (!is_array($this->getData('uuids'))) {
            if (ENABLE_DEBUG_MODE) {
                throw $this->exception('Bad Request. \'uuids\' no array.', 400);
            } else {
                throw $this->exception('Bad Request.', 400);
            }
        }
        $this->uuids = $this->getJSON()['uuids'];
        // validate uuids
        foreach ($this->uuids as $uuid => $options) {
            if (!MinecraftLinkerUtil::validUUID($uuid)) {
                unset($this->uuids[$uuid]);
            }
        }
        if (empty($this->uuids)) {
            if (ENABLE_DEBUG_MODE) {
                throw $this->exception('Bad Request. \'uuids\' contains no valid uuid.', 400);
            } else {
                throw $this->exception('Bad Request.', 400);
            }
        }

        // check name
        if ($this->ignoreName) {
            return;
        }

        if (empty(array_values($this->uuids))) {
            if (ENABLE_DEBUG_MODE) {
                throw $this->exception('Bad Request. \'uuids\' contains no options.', 400);
            } else {
                throw $this->exception('Bad Request.', 400);
            }
        }
        foreach ($this->uuids as $uuid => $options) {
            if (!array_key_exists('name', $options)) {
                unset($this->uuids[$uuid]);
            }
        }
        if (empty($this->uuids)) {
            if (ENABLE_DEBUG_MODE) {
                throw $this->exception('Bad Request. \'uuids\' contains no valid names in options.', 400);
            } else {
                throw $this->exception('Bad Request.', 400);
            }
        }
    }
}
