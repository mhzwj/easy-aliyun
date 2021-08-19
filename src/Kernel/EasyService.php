<?php


namespace mhzwj\aliyun\Kernel;

use GuzzleHttp\Client;

/**
 * 基础服务
 * Class EasyService
 * @package mhzwj\aliyun\Kernel
 */
class EasyService
{

    /**
     * GuzzleHttp\Client
     * @var array
     */
    protected $guzzleOption = [];

    /**
     * AliyunLib constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {

        foreach ($config as $attr => $val) {
            $key        = lcfirst($attr);
            $this->$key = $val;
        }
    }

    /**
     * @param $url
     * @param array $query
     * @param array $headers
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($url, $query = [], $headers = [])
    {
        $resp = $this->httpClient()->request('get', $url, [
            'headers' => $headers,
            'query'   => $query,
        ]);
        return $resp->getBody()->getContents();
    }

    /**
     * @param $url
     * @param array $params
     * @param array $headers
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($url, $params = [], $headers = [])
    {
        $resp = $this->httpClient()->request('post', $url, [
            'headers'     => $headers,
            'form_params' => $params,
        ]);
        return $resp->getBody()->getContents();
    }


    /**
     * @return Client
     */
    public function httpClient(): Client
    {
        return new Client($this->guzzleOption);
    }
}