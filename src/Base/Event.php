<?php

namespace Kang\Libs\Base;

class Event{
    public $name; //事件名称
    public $sender; //发起事件的对象
    public $handled = false;
    public $data; //携带的数据
    public $tag; //标签
}