<?php

namespace mhzwj\aliyun;

use mhzwj\aliyun\Official\DysmsService;
use mhzwj\aliyun\Official\StsService;
use PHPUnit\Framework\TestCase;

/**
 * Class LibTest
 * @package mhzwj\aliyun
 */
class OfficialFactoryTest extends TestCase
{
    /**
     *
     */
    public function testSms()
    {
        $sms = OfficialFactory::Dysms([]);
        $this->assertEquals(new DysmsService(), $sms);
    }

    public function testSts()
    {
        $sms = OfficialFactory::Sts([]);
        $this->assertEquals(new StsService(), $sms);
    }
}