<?php


namespace mhzwj\aliyun;


/**
 * 云市场
 * Class MarketFactory
 * @package mhzwj\aliyun
 * @method static \mhzwj\aliyun\Market\IdcardcheckService Idcardcheck(array $config)
 */
class MarketFactory
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        $application = __NAMESPACE__ . '\\Market\\' . $name . 'Service';
        if (!class_exists($application)) {
            throw new \Exception($application . 'class not found');
        }
        return new $application(...$arguments);
    }

}