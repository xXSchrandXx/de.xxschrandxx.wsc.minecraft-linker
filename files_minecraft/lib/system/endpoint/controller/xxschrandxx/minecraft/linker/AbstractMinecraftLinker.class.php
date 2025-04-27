<?php

namespace minecraft\system\endpoint\controller\xxschrandxx\minecraft\linker;

use wcf\http\Helper;
use minecraft\system\endpoint\controller\xxschrandxx\minecraft\AbstractMinecraft;
use wcf\system\exception\UserInputException;

/** /xxschrandxx/minecraft/{uuid:[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}} &{name} */
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
    public function validate(): void
    {
        parent::validate();

        $this->uuid = $this->variables['uuid'];

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
