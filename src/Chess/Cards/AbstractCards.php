<?php
namespace Kang\Libs\Chess\Cards;

/**
 * @var 棋牌获取牌组类
 * Class Cards
 */
abstract class AbstractCards {
    public function __construct(array $config = []){
        $this->configure($config);
        $this->__init();
    }

    public $removeCard = []; //要移除的卡牌
    public $number = 1; //使用几副牌
    public $playerNum; //玩家数量
    public $cardNum; //每人卡牌数量
    public $dealNum = 1; //每次连续发牌数
    /**
     * @var 发牌
     * @param array $cheat 作弊牌
     * @param int $cheatNum 作弊牌组下标
     * @return array
     */
    public function licensing($cheat = [], $cheatNum = 0){
        $players = $this->getCheatOut($cheat, $cheatNum);
        $cards = $this->_cards;
        $current = 1;
        $total = ceil($this->cardNum / $this->dealNum) + 1;
        while($current < $total){
            foreach($players as $key => $player){
                $card = array_splice($cards, 0,  ($this->cardNum - count($player)) );
                $players[$key] = array_merge($players[$key], $card);
            }

            $current++;
        }

        return [
            'player' => $players,
            'cards' => $cards
        ];
    }

    private function getCheatOut($cheat, $cheatNum){
        if($cheatNum >= $this->playerNum){
            throw new \Exception('作弊玩家不存在!');
        }

        $players = [];
        for($i = 0; $i < $this->playerNum; $i++){
            $players[$i] = [];
        }

        $len = count($this->_cards);
        $this->_cheatNum = count($cheat);
        $cards = array_diff($this->_cards, $cheat);
        if($len != (count($cards) + $this->_cheatNum)){
            throw new \Exception('作弊牌堆错误!');
        }

        $this->_cards = $cards;
        $players[$cheatNum] = $cheat;
        return $players;
    }
    /**
     * 设置牌组
     * @return $this
     */
    protected function __init(){
        $diffCards  = $this->getDiffCards();
        $cards = $diffCards;
        $number = $this->number;
        while($number > 1){
            $cards = array_merge($cards, $diffCards);
            $number--;
        }

        shuffle($cards);
        $this->_cards = $cards;
        if(count($cards) < ($this->cardNum * $this->dealNum)){
            throw new \Exception('牌组不过发牌!');
        }
    }
    /**
     * @return array
     */
    private function getDiffCards(){
        if($this->_diffCards !== null){
            return $this->_diffCards;
        }else if(count($this->removeCard) > 0){
            $this->_diffCards = array_diff(static::$cards, $this->removeCard);
        }else{
            $this->_diffCards = static::$cards;
        }

        return  $this->_diffCards;
    }
    /**
     * 快速对象属性注册
     * @param Object $object
     * @param array $properties 字典数组
     * */
    public function configure( $properties = []){
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }
    /**
     * @var 初始化牌组
     * @var array
     */
    private $_cards = [];
    private $_diffCards = null;
    private $_cheatNum = 0;
}
