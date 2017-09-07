<?php

namespace creative\foundation\tests\lib\routing;

class NotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHttpStatus()
    {
        $exception = new \creative\foundation\routing\NotFoundException;

        $this->assertSame(
            '404 Not Found',
            $exception->getHttpStatus()
        );
    }
}
