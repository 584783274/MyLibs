<?php

namespace Kang\Libs\Base;

/**
 * @var 行为观察者
 * Class Behavior
 * @package MyLibs\Event
 */
class Behavior{

    public $owner;

    public function events(){
        return [];
    }

    public function attach(Component $owner){
        $this->owner = $owner;
        foreach ($this->events() as $event => $handler) {
            $owner->on($event, is_string($handler) ? [$this, $handler] : $handler);
        }
    }
}
