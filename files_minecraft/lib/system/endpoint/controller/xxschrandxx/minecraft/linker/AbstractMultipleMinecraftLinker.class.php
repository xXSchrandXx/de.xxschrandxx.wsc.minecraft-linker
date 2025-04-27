<?php

namespace minecraft\system\endpoint\controller\xxschrandxx\minecraft\linker;

use wcf\http\Helper;
use minecraft\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\exception\UserInputException;
use minecraft\util\MinecraftLinkerUtil;

/** /xxschrandxx/minecraft/ */
abstract class AbstractMultipleMinecraftLinker extends AbstractMinecraft
{
    /**
     * Weather the request requires a name
     * @var bool
     */
    public bool $ignoreName = true;

    /**
     * Minecraft array($uuid => $name) for this request
     * If self::$ifnoreName ist true, names will be empty
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
        foreach (array_Keys($parameters->uuids) as $uuid) {
            if (!MinecraftLinkerUtil::validUUID($uuid))
                unset($parameters->uuids[$uuid]);
        }

        if (empty($parameters->uuids))
            throw new UserInputException('uuids');

        if ($this->ignoreName) {
            foreach ($parameters->uuids as $uuid => $name) {
                $parameters->uuids[$uuid] = '';
            }
            $this->uuids = $parameters->uuids;
        } else {
            if (empty(array_values($parameters->uuids))) {
                if (ENABLE_DEBUG_MODE)
                    throw new UserInputException('uuids', 'contains no names.');
                else
                    throw new UserInputException();
            }
            foreach ($parameters->uuids as $uuid => $name) {
                if (!isset($name) || empty($name))
                    unset($parameters->uuids[$uuid]);
            }

            $this->uuids = $parameters->uuids;
        }

        if (empty($this->uuids)) {
            if (ENABLE_DEBUG_MODE)
                throw new UserInputException('uuids', 'contains no valid names.');
            else
                throw new UserInputException();
        }
    }
}

/** @internal */
class MultipleMinecraftLinkerParameters
{
    public function __construct(
        /** @var string[] */
        public array $uuids
    ) {
    }
}
