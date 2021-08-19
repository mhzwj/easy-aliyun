
# [MQTT产品](https://www.aliyun.com/product/mq4iot)

> 微消息队列MQTT版是阿里云推出的一款面向移动互联网以及物联网领域的轻量级消息中间件。如果说传统的消息队列中间件一般应用于微服务之间，那么适用于物联网的微消息队列MQTT版则实现了端与云之间的消息传递和真正意义上的万物互联。本文介绍微消息队列MQTT版的系统架构、应用场景和产品优势。


| 默认可选参数key值 | 默认值                                  | 描述                                                         |
| ----------------- | :-------------------------------------- | ------------------------------------------------------------ |
| port              | 80                                      | 标准协议端口                                                 |
| sslPort           | 8883                                    | SSL 端口                                                     |
| webSocketPort     | 80                                      | WebSocket 端口                                               |
| webSocketSslPort  | 443                                     | WebSocket SSL/TLS 端口                                       |
| flashPort         | 843                                     | Flash 端口                                                   |
| useTLS            | false                                   | 使用 HTTPS 加密则配置为 true                                 |
| endpoint          | onsmqtt.mq-internet-access.aliyuncs.com | [API接入地域](https://help.aliyun.com/document_detail/181438.html) |
| regionId          | cn-hangzhou                             | [实例所在地域](https://help.aliyun.com/document_detail/181438.html) |

~~~
$mqtt = \mhzwj\aliyun\OfficialFactory::Mqtt([
   //账号的 AccessKey，在阿里云控制台查看
  'acessKeyID' => 'XXXX',
  //账号的的 SecretKey，在阿里云控制台查看
  'accessKeySecret' => 'XXXX', 
  // 设置当前用户的接入点域名，接入点获取方法请参考接入准备章节文档，先在控制台创建实例
  'mqttEndpoint' => 'XXXX.mqtt.aliyuncs.com',
   //实例 ID，购买后从控制台获取
  'instanceId' => 'XXXX',
  //MQTT GroupID,创建实例后从 MQTT 控制台创建
  'groupId' => 'GID_XXXX',
  //需要操作的 Topic,第一级父级 topic 需要在控制台申请
  'topic'=>'XXXX', 
  //当前客户端唯一表示
  'deviceId' => 'XXXX',
])
# 获取客户端参数
$mqtt->getClientInfo($deviceId)
# p2p 发送消息
$mqtt->p2pPublish(deviceId, '我是测试');
# 自定义发送消息内容 $clientId 生成规则：$topic + $groupId + '@@@' + $deviceId 例如:'topic/p2p/GID_XXXX@@@00001'
$mqtt->publish($clientId, '我是测试');
# 通过endpoint获取客户端是否在线
# 具体实现方式：https://help.aliyun.com/document_detail/178121.html
$mqtt->querySessionByDeviceId($deviceId);
~~~

> 在使用过程中，如果出现超时情况，导致的原因是其中参数错误

# [短信服务](https://www.aliyun.com/product/sms)

> 短信服务（Short Message Service）是广大企业客户快速触达手机用户所优选使用的通信能力。调用API或用群发助手，即可发送验证码、通知类和营销类短信；国内验证短信秒级触达，到达率最高可达99%；国际/港澳台短信覆盖200多个国家和地区，安全稳定，广受出海企业选用。

| 默认可选参数key值 | 默认值                       | 描述                                                         |
| ----------------- | :--------------------------- | ------------------------------------------------------------ |
| endpoint          | http://dysmsapi.aliyuncs.com | 接入地址                                                     |
| regionId          | cn-hangzhou                  | [API支持的RegionID，如短信API的值为：cn-hangzhou](https://next.api.aliyun.com/api/Dysmsapi/2017-05-25/SendSms?params={}) |
~~~
$sms = \mhzwj\aliyun\OfficialFactory::Dysms([
  'acessKeyID' => 'XXXX',//账号的 AccessKey，在阿里云控制台查看
  'accessKeySecret' => 'XXXX', //账号的的 SecretKey，在阿里云控制台查看
  'signName'=>'阿里云签名'
])
$sms->send($phone, [
  'template_code' => 'SMS_XXXX',
  'code' => 1111
]);
~~~

# [STS服务](https://help.aliyun.com/document_detail/28756.html)

>  阿里云临时安全令牌（Security Token Service，STS）是阿里云提供的一种临时访问权限管理服务。
>
>  基于RAM角色实现跨账号访问OSS： https://help.aliyun.com/document_detail/176941.html
>
>  OSS直传服务：https://help.aliyun.com/document_detail/31920.html


| 默认可选参数key值 | 默认值                   | 描述     |
| ----------------- | :----------------------- | -------- |
| endpoint          | https://sts.aliyuncs.com | 接入地址 |
| policy            | 无                       | 权限策略 |
~~~
$sts = \mhzwj\aliyun\OfficialFactory::Sts([
  'acessKeyID' => 'XXXX',//账号的 AccessKey，在阿里云控制台查看
  'accessKeySecret' => 'XXXX', //账号的的 SecretKey，在阿里云控制台查看
  'accountID' => 'XXXX',//阿里云账号ID。您可以通过登录阿里云控制台，将鼠标悬停在右上角头像的位置，单击安全设置进行查看。
  'roleName' => 'XXXX',//RAM角色名称。您可以通过登录RAM控制台，单击左侧导航栏的RAM角色管理，在RAM角色名称列表下进行查看。
]);
//用户自定义参数。此参数用来区分不同的令牌，可用于用户级别的访问审计。
//过期时间，单位为秒。
$sts->assumeRole($roleSessionName = 'alice', $durationSeconds = 3600);
~~~

