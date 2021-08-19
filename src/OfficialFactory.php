<?php


namespace mhzwj\aliyun;


/**
 * 阿里云官方
 * Class official
 * @package mhzwj\aliyun
 * @method static \mhzwj\aliyun\Official\MqttService  Mqtt(array $config)
 * @method static \mhzwj\aliyun\Official\DysmsService Dysms(array $config)
 * @method static \mhzwj\aliyun\Official\StsService   Sts(array $config)
 *
 */
class OfficialFactory
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        $application = __NAMESPACE__ . '\\Official\\' . $name . 'Service';
        if (!class_exists($application)) {
            throw new \Exception($application . 'class not found');
        }
        return new $application(...$arguments);
    }
}