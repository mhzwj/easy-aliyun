<?php

namespace mhzwj\aliyun\Market;

use mhzwj\aliyun\Kernel\MarketService;

/**
 * Class IdcardcheckService
 * @package mhzwj\aliyun\Market
 */
class IdcardcheckService extends MarketService
{

    /**
     * 身份证二要素实名核验
     * https://market.aliyun.com/products/57126001/cmapi00040219.html
     * @param $name
     * @param $idcard
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function shumaidata($name, $idcard)
    {
        $endpoint = 'https://idcardcheck.shumaidata.com/idcardcheck';
        $query    = [
            'idcard' => $idcard,
            'name'   => ($name),
        ];
        $headers  = [
            'Authorization' => 'APPCODE ' . $this->appCode
        ];
        return $this->get($endpoint, $query, $headers);

    }
}