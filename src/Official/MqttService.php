<?php

namespace mhzwj\aliyun\Official;


use mhzwj\aliyun\Kernel\OfficialService;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;


/**
 * Class MqttService
 * @package mhzwj\aliyun\Official
 */
class MqttService extends OfficialService
{

    /**
     * 接口请求地址
     * https://help.aliyun.com/document_detail/181438.html
     * @var
     */
    protected $endpoint = 'onsmqtt.mq-internet-access.aliyuncs.com';

    /**
     * @var
     * 微消息队列MQTT版实例所在地域（Region)
     * https://help.aliyun.com/document_detail/181438.html
     */
    protected $regionId = 'cn-hangzhou';

    /**
     * 接入点地址，购买实例后从控制台获取
     * @var
     */
    protected $mqttEndpoint;

    /**
     * @var int
     * 标准协议端口
     */
    protected $port = 1883;
    /**
     * @var int
     * SSL 端口
     */
    protected $sslPort = 8883;
    /**
     * @var int
     * WebSocket 端口
     */
    protected $webSocketPort = 80;

    /**
     * @var int
     * WebSocket SSL/TLS 端口
     */
    protected $webSocketSslPort = 443;

    /**
     * @var int
     * Flash 端口
     */
    protected $flashPort = 843;

    /**
     * 实例 ID，购买后从控制台获取
     * @var
     */
    protected $instanceId;

    /**
     * MQTT 客户端ID 前缀， GroupID，需要在 MQTT 控制台申请
     * @var
     */
    protected $groupId;
    /**
     * @var
     * 需要操作的 Topic,第一级父级 topic 需要在控制台申请
     */
    protected $topic;

    /**
     * MQTT 客户端ID 后缀，DeviceId，业务方自由指定，需要保证全局唯一，禁止 2 个客户端连接使用同一个 ID
     * @var
     */
    protected $deviceId;
    /**
     * @var
     */
    protected $clientId;

    /**
     * @var bool
     * 如果使用 HTTPS 加密则配置为 true
     */
    protected $useTLS = false;
    /**
     * @var int
     */
    protected $connectTimeout = 5;
    /**
     * @var int
     */
    protected $socketTimeout = 5;
    /**
     * @var int
     */
    protected $resendTimeout = 5;
    /**
     * @var MqttClient
     */
    protected $mqtt;

    /**
     * Application constructor.
     * @param array $config
     * @throws \PhpMqtt\Client\Exceptions\ConfigurationInvalidException
     * @throws \PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException
     * @throws \PhpMqtt\Client\Exceptions\ProtocolNotSupportedException
     */

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        // clientId
        $this->clientId = $this->clientId($this->deviceId);
        // connect
        $this->mqtt = $this->setMqttClient();
        //close
        register_shutdown_function(function () {
            $this->mqtt->disconnect();
        });
    }

    /**
     * @param $toDeviceId
     * @param $message
     * @return mixed
     * @throws \PhpMqtt\Client\Exceptions\ConfigurationInvalidException
     * @throws \PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException
     * @throws \PhpMqtt\Client\Exceptions\DataTransferException
     * @throws \PhpMqtt\Client\Exceptions\ProtocolNotSupportedException
     * @throws \PhpMqtt\Client\Exceptions\RepositoryException
     */
    public function p2pPublish(string $toDeviceId, string $message)
    {
        $p2p_topic = $this->topic . '/p2p/' . $this->clientId($toDeviceId);
        return $this->publish($p2p_topic, $message);
    }

    /**
     * @param string $topic
     * @param string $message
     * @param int $qualityOfService
     * @param bool $retain
     * @return mixed
     * @throws \PhpMqtt\Client\Exceptions\DataTransferException
     * @throws \PhpMqtt\Client\Exceptions\RepositoryException
     */
    public function publish(string $topic, string $message, int $qualityOfService = 0, bool $retain = false)
    {
        $this->mqttClient()->publish($topic, $message, $qualityOfService, $retain);
        return $this->deviceId;
    }

    /**
     * 获取客户端
     * @param $deviceId
     * @return array
     */
    public function getClientInfo($deviceId)
    {
        return [
            'endpoint'      => $this->mqttEndpoint,
            'useTLS'        => $this->useTLS,
            'port'          => $this->port(),
            'webSocketPort' => $this->webSocketPort(),
            'username'      => $this->username(),
            'password'      => $this->password(),
            'clientId'      => $this->clientId($deviceId),
        ];
    }

    /**
     * @param $deviceId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function querySessionByDeviceId($deviceId)
    {
        $params              = array_merge($this->publicParams(), [
            'Action'     => 'QuerySessionByClientId',
            'ClientId'   => $this->clientId($deviceId),
            'InstanceId' => $this->instanceId,
            'RegionId'   => $this->regionId,
        ]);
        $params['Signature'] = $this->generateSign($params);
        return $this->get($this->endpoint, $params);
    }

    /**
     * 阿里云
     * https://help.aliyun.com/document_detail/163046.html
     * @return array
     */
    protected function publicParams()
    {
        return [
            'Format'           => $this->format,
            'Version'          => '2020-04-20',
            'AccessKeyId'      => $this->acessKeyID,
            'SignatureMethod'  => 'HMAC-SHA1',
            'Timestamp'        => $this->timestamp(),
            'SignatureVersion' => '1.0',
            'SignatureNonce'   => $this->signatureNonce(),
        ];
    }

    /**
     * @param $deviceId
     * @return string
     */
    public function clientId($deviceId)
    {
        return $this->groupId . '@@@' . $deviceId;
    }

    /**
     * @return MqttClient
     */
    public function mqttClient(): MqttClient
    {
        return $this->mqtt;
    }

    /**
     * @return MqttClient
     * @throws \PhpMqtt\Client\Exceptions\ConfigurationInvalidException
     * @throws \PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException
     * @throws \PhpMqtt\Client\Exceptions\ProtocolNotSupportedException
     */
    protected function setMqttClient()
    {
        $mqtt = new MqttClient($this->mqttEndpoint, $this->port(), $this->clientId);
        $mqtt->connect($this->connectionSettings(), true);
        return $mqtt;
    }

    /**
     * @return ConnectionSettings
     */
    protected function connectionSettings(): ConnectionSettings
    {
        return (new ConnectionSettings())
            ->setUsername($this->username())
            ->setPassword($this->password())
            ->setUseTls($this->useTLS)
            ->setSocketTimeout($this->socketTimeout)
            ->setResendTimeout($this->resendTimeout)
            ->setConnectTimeout($this->connectTimeout);
    }

    /**
     * @return int
     */
    protected function port()
    {
        return $this->useTLS ? $this->sslPort : $this->port;
    }

    /**
     * @return int
     */
    protected function webSocketPort()
    {
        return $this->useTLS ? $this->webSocketSslPort : $this->webSocketPort;
    }

    /**
     * @return string
     */
    protected function username()
    {
        return 'Signature|' . $this->acessKeyID . '|' . $this->instanceId;
    }

    /**
     * @return string
     */
    protected function password()
    {
        $hash = hash_hmac('sha1', $this->clientId, $this->accessKeySecret, true);
        return base64_encode($hash);
    }
}
