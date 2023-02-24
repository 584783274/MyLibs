<?php

namespace Kang\Libs\WeChat\Library;
/**
 * OpenApi 管理
 * Trait OpenApi
 * @package Kang\Libs\WeChat\Library
 */
trait OpenApi{
    /**
     * @var 清空 api 的调用quota
     * @return bool
     */
    public function openApiClearQuota(){
        $data['appid'] = $this->appid;
        if(!$this->httpPost(Urls::OPEN_API_CLEAR_QUOTA, $data, true)){
            return false;
        }

        return true;
    }

    /**
     * @var 查询 openAPI 调用quota
     * @return bool| ['daily_limit' => '当天该账号可调用该接口的次数', 'used' => '当天已经调用的次数', 'remain' =>  '当天剩余调用次数']
     */
    public function openApiGetQuota(){
        $data['appid'] = $this->appid;
        if(!$result = $this->httpPost(Urls::OPEN_API_GET_QUOTA, $data, true)){
            return $result['quota'];
        }

        return true;
    }

    /**
     * @var 调用接口报错返回的rid
     * @param string $rid
     * @return bool | [
            invoke_time	timestamp	发起请求的时间戳
            cost_in_ms	Number	请求毫秒级耗时
            request_url	String	请求的 URL 参数
            request_body	String	post请求的请求参数
            response_body	String	接口请求返回参数
            client_ip	String	接口请求的客户端ip
     * ]
     */
    public function openApiGetRid($rid){
        $data['rid'] = $rid;
        if(!$result = $this->httpPost(Urls::OPEN_API_RID_QUOTA, $data, true)){
            return $result['request'];
        }

        return true;
    }
}
