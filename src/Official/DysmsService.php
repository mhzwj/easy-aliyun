<?php


namespace mhzwj\aliyun\Official;

use mhzwj\aliyun\Kernel\OfficialService;

/**
 * Class DysmsService
 * @package mhzwj\aliyun\Official
 */
class DysmsService extends OfficialService
{
    /**
     * @var string
     */
    protected $endpoint = 'http://dysmsapi.aliyuncs.com';
    /**
     * @var string
     * API支持的RegionID，如短信API的值为：cn-hangzhou
     * https://next.api.aliyun.com/api/Dysmsapi/2017-05-25/SendSms?params={}
     *
     */
    protected $regionId = 'cn-hangzhou';

    /**
     * @var
     * 短信签名名称。请在控制台国内消息或国际/港澳台消息页面中的签名管理页签下签名名称一列查看。
     */
    protected $signName;

    /**
     * Sms constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * 公共参数
     * @return array
     */
    protected function publicParams()
    {
        return [
            'RegionId'         => $this->regionId,
            'AccessKeyId'      => $this->acessKeyID,
            'Format'           => $this->format,
            'SignatureMethod'  => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'Timestamp'        => $this->timestamp(),
            'Version'          => '2017-05-25',
            'SignatureNonce'   => $this->signatureNonce(),
        ];
    }

    /**
     * 发送短信
     * @param $phone
     * @param $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($phone, $data)
    {
        $signName = isset($data['signName']) ? $data['signName'] : $this->signName;
        unset($data['signName']);
        $params              = array_merge($this->publicParams(), [
            'Action'        => 'SendSms',
            'PhoneNumbers'  => $phone,
            'SignName'      => $signName,
            'TemplateCode'  => $data['template_code'],
            'TemplateParam' => json_encode($data, JSON_FORCE_OBJECT),
        ]);
        $params['Signature'] = $this->generateSign($params);
        return $this->get($this->endpoint, $params);
    }

}