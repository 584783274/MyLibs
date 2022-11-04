<?php
namespace Kang\Libs\Chess\Cards;

/**
 * @var 扑克牌类
 * Class Chess
 */
class PlayingCards extends AbstractCards{
    /**
     * @var S14 代表小王, S15代表大王
     * @var array
     */
    public static $cards = [
        '01A', '02A', '03A',  '04A',  '05A',  '06A',  '07A',  '08A',  '09A',  '10A',  '11A',  '12A',  '13A',
        '01B', '02B', '03B',  '04B',  '05B',  '06B',  '07B',  '08B',  '09B',  '10B',  '11B',  '12B',  '13B',
        '01C', '02C', '03C',  '04C',  '05C',  '06C',  '07C',  '08C',  '09C',  '10C',  '11C',  '12C',  '13C',
        '01D', '02D', '03D',  '04D',  '05D',  '06D',  '07D',  '08D',  '09D',  '10D',  '11D',  '12D',  '13D',
        '14', '15'
    ];
}
