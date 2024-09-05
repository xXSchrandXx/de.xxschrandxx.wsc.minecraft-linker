<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use wcf\http\Helper;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\exception\UserInputException;
use wcf\util\MinecraftLinkerUtil;

/** /xxschrandxx/minecraft/ */
abstract class AbstractMultipleMinecraftLinker extends AbstractMinecraft
{
    /**
     * Weather the request requires a name
     * @var bool
     */
    public bool $ignoreName = true;

    /**
     * Minecraft UUID[] for this request
     * @var array
     */
    public $uuids;

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        parent::validate();

        $parameters = Helper::mapApiParameters($this->request, MultipleMinecraftLinkerParameters::class);
        if (!isset($parameters->uuids))
            throw new UserInputException('uuids');

        // validate uuids
        foreach ($parameters->uuids as $uuid => $options) {
            if (!MinecraftLinkerUtil::validUUID($uuid))
                unset($parameters->uuids[$uuid]);
        }

        if (empty($parameters->uuids))
            throw new UserInputException('uuids');

        $this->uuids = $parameters->uuids;

        if ($this->ignoreName)
            return;

        if (empty(array_values($parameters->uuids))) {
            if (ENABLE_DEBUG_MODE)
                throw new UserInputException('uuids', 'contains no options.');
            else
                throw new UserInputException();
        }
        foreach ($parameters->uuids as $uuid => $options) {
            if (!array_key_exists('name', $options)) {
                unset($parameters->uuids[$uuid]);
            }
        }
        if (empty($parameters->uuids)) {
            if (ENABLE_DEBUG_MODE)
                throw new UserInputException('uuids', 'contains no valid names in options.');
            else
                throw new UserInputException();
        }
    }
}

/** @internal */
class MultipleMinecraftLinkerParameters
{
    public function __construct(
        /** @var non-empty-array */
        public readonly array $uuids,
    ) {
    }
}
