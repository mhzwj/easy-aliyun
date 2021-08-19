<?php


namespace mhzwj\aliyun\Official;

use mhzwj\aliyun\Kernel\OfficialService;


/**
 * Class StsService
 * @package mhzwj\aliyun\Official
 */
class StsService extends OfficialService
{

    /**
     * 接入地址
     * @var string
     * https://help.aliyun.com/document_detail/66053.html
     */
    protected $endpoint = 'https://sts.aliyuncs.com';

    /**
     * @var string
     * 阿里云账号ID。您可以通过登录阿里云控制台，将鼠标悬停在右上角头像的位置，单击安全设置进行查看。
     */
    protected string $accountID;

    /**
     * @var
     * RAM角色名称。您可以通过登录RAM控制台，单击左侧导航栏的RAM角色管理，在RAM角
     */
    protected string $roleName;

    /**
     * @var
     * 权限策略
     */
    protected string $policy = '';

    /**
     * Sts constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * 公共参数
     * @return array
     * https://help.aliyun.com/document_detail/28759.html
     */
    protected function publicParams()
    {
        return [
            'Format'           => $this->format,
            'Version'          => '2015-04-01',
            'SignatureMethod'  => 'HMAC-SHA1',
            'SignatureNonce'   => $this->signatureNonce(),
            'SignatureVersion' => '1.0',
            'AccessKeyId'      => $this->acessKeyID,
            'Timestamp'        => $this->timestamp(),
        ];
    }

    /**
     * 调用AssumeRole接口获取一个扮演该角色的临时身份，此处RAM用户扮演的是受信实体为阿里云账号类型的RAM角色。
     * https://help.aliyun.com/document_detail/28763.html
     * @param string $roleSessionName 用户自定义参数。此参数用来区分不同的令牌，可用于用户级别的访问审计
     * @param int $durationSeconds 过期时间，单位为秒。
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function assumeRole(string $roleSessionName = 'alice', int $durationSeconds = 3600)
    {
        $params = array_merge($this->publicParams(), [
            'Action'          => 'AssumeRole',
            'RoleArn'         => $this->buildRoleArn(),
            'RoleSessionName' => $roleSessionName,
            'DurationSeconds' => $durationSeconds
        ]);
        if (!empty($this->policy)) {
            $params['Policy'] = $this->policy;
        }
        $params['Signature'] = $this->generateSign($params);
        return $this->get($this->endpoint, $params);
    }

    /**
     * 调用GetCallerIdentity接口获取当前调用者的身份信息。
     * https://help.aliyun.com/document_detail/43767.html
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCallerIdentity()
    {
        $params              = array_merge($this->publicParams(), [
            'Action' => 'GetCallerIdentity',
        ]);
        $params['Signature'] = $this->generateSign($params);
        return $this->get($this->endpoint, $params);
    }

    /**
     * RoleArn 角色账号生成
     * @return string
     */
    protected function buildRoleArn()
    {
        return 'acs:ram::' . $this->accountID . ':role/' . strtolower($this->roleName);
    }
}