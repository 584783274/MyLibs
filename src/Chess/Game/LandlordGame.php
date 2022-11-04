<?php
namespace Kang\Libs\Chess\Game;
use MyLibs\Chess\Cards\PlayingCards;

/**
 * @var 斗地主
 * Class LandlordGame
 */
class LandlordGame implements InterfaceGame{
    public function __construct($removeCard = [], $number = 1){
        $this->_removeCard = $removeCard;
        $this->_number = $number;
    }

    /**
     * @var 获取发牌结果
     * @return array
     */
    public function licensing($cheat = [], $cheatNum = 0) : array {
        $playingCards =  $this->getPlayingCards();
        $list = $playingCards->licensing($cheat, $cheatNum);
        $this->getSortCards($list);
        return $list;
    }

    public function validatePlayCards(array $cards){

    }
    /**
     * @var 获取卡牌对象
     * @return PlayingCards|null
     */
    private function getPlayingCards(){
        if(!($this->_playingCards instanceof  PlayingCards)){
            $this->_playingCards = new PlayingCards([
                'removeCard' => $this->_removeCard,
                'playerNum' => $this->_playerNum,
                'number' => $this->_number,
                'cardNum' => $this->_cardNum,
                'dealNum' => $this->_dealNum,
            ]);
        }

        return $this->_playingCards;
    }

    /**
     * @var 牌组排序
     * @param array $cards ['player'] 玩家牌堆
     */
    public function getSortCards(&$list){
        foreach($list['player'] as $key => $items){
            $list['player'][$key] = $this->sort($items);
        }
    }

    private function sort($items){
        sort($items);
        $key_1 = $key_2 = 0;
        foreach($items as $key => $item){
            $item = intval($item);
            if($item < 3){
                $key_1 = $key + 1;
            }

            if($item < 14){
                $key_2 = $key - $key_1 + 1;
            }
        }

        $card_1 = array_splice($items, 0, $key_1);
        $card_2 = array_splice($items, 0, $key_2);

        return array_merge($card_2, $card_1, $items);
    }

    protected $_playerNum = 3;      //玩家
    protected $_dealNum = 3;        //连续发牌数量
    protected $_cardNum = 17;       //每组总牌数
    protected $_number = 1;         //几副牌
    protected $_removeCard = [];   //去除掉的牌组
    private $_playingCards = null;
}
