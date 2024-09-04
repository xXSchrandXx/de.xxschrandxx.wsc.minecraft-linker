<?php

namespace wcf\system\endpoint\controller\xxschrandxx\minecraft\linker;

use wcf\http\Helper;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\exception\UserInputException;

/**
 * Path: /xxschrandxx/minecraft/{id:\d+}/{uuid:[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}}/{name}
 */
abstract class AbstractMinecraftLinker extends AbstractMinecraft
{
    /**
     * Minecraft UUID for this request
     * @var string
     */
    public $uuid;

    /**
     * Weather the request requires a name
     * @var bool
     */
    public bool $ignoreName = true;

    /**
     * Minecraft Name for this request
     * @var string
     */
    public $name;

    /**
     * @inheritDoc
     */
    public function validateVariables(array $variables): void
    {
        parent::validateVariables($variables);

        if (!array_key_exists('uuid', $variables))
            throw new UserInputException('uuid');
        $this->uuid = $variables['uuid'];

        if ($this->ignoreName)
            return;

        $parameters = Helper::mapApiParameters($this->request, MinecraftLinkerParameters::class);
        if (!isset($parameters->name))
            throw new UserInputException('name');
        $this->name = $parameters->name;
    }
}

/** @internal */
class MinecraftLinkerParameters
{
    public function __construct(
        /** @var string */
        public readonly string $name,
    ) {
    }
}
