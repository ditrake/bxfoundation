<?php

namespace creative\foundation\tests\lib\routing;

class HttpExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHttpStatus()
    {
        $exception = new \creative\foundation\routing\HttpException;

        $this->assertSame(
            '500 Internal Server Error',
            $exception->getHttpStatus()
        );
    }
}
