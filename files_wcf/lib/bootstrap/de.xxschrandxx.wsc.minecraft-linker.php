<?php

use wcf\event\endpoint\ControllerCollecting;
use minecraft\system\endpoint\controller\xxschrandxx\minecraft\linker\GetCode;
use minecraft\system\endpoint\controller\xxschrandxx\minecraft\linker\GetLinked;
use minecraft\system\endpoint\controller\xxschrandxx\minecraft\linker\GetUnlinked;
use minecraft\system\endpoint\controller\xxschrandxx\minecraft\linker\UpdateNames;
use wcf\system\event\EventHandler;

return static function (): void {
    EventHandler::getInstance()->register(
        ControllerCollecting::class,
        static function (ControllerCollecting $event) {
            $event->register(new GetCode());
            $event->register(new GetLinked());
            $event->register(new GetUnlinked());
            $event->register(new UpdateNames());
        }
    );
};
