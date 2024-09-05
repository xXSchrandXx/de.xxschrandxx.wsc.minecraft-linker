<?php

use wcf\event\endpoint\ControllerCollecting;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\linker\GetCode;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\linker\getLinked;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\linker\GetUnlinked;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\linker\UpdateNames;
use wcf\system\event\EventHandler;

return static function (): void {
    EventHandler::getInstance()->register(
        ControllerCollecting::class,
        static function (ControllerCollecting $event) {
            $event->register(new GetCode());
            $event->register(new getLinked());
            $event->register(new GetUnlinked());
            $event->register(new UpdateNames());
        }
    );
};
