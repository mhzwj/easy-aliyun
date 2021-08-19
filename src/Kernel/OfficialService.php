<?php

namespace mhzwj\aliyun\Kernel;

/**
 * 官方基础服务
 * Class OfficialService
 * @package mhzwj\aliyun\Kernel
 */
class OfficialService extends EasyService
{

    /**
     * 此处填写阿里云帐号 AccessKey ID
     * @var
     */
    protected $acessKeyID;
    /**
     * 此处填写阿里云帐号 AccessKey Secret
     * @var
     */
    protected $accessKeySecret;
    /**
     * @var string
     * 返回参数的语言类型。取值范围：json | xml。默认值：json。
     */
    protected $format = 'json';

    /**
     * 2021-08-11T03:00:40Z
     * @return false|string
     */
    protected function timestamp()
    {
        return gmdate('Y-m-d\TH:i:s\Z');
    }
    /**
     * Wed, 11 Aug 2021 02:59:37 GMT
     * @return string
     */
    public function getTimeGMT()
    {
        return gmdate('D, d M Y H:i:s') . ' GMT';
    }

    /**
     * 唯一随机数，用于防止网络重放攻击。
     * @return string
     */
    public function signatureNonce()
    {
        return uniqid();
    }

    /**
     * 阿里云签名生成
     * @param $params
     * @return string
     */
    protected function generateSign($params)
    {
        ksort($params);
        $accessKeySecret = $this->accessKeySecret;
        $stringToSign    = 'GET&%2F&' . urlencode(http_build_query($params, null, '&', PHP_QUERY_RFC3986));
        return base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
    }


}