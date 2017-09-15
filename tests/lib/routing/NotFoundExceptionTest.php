<?php

namespace marvin255\bxfoundation\tests\lib\routing;

class NotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHttpStatus()
    {
        $exception = new \marvin255\bxfoundation\routing\NotFoundException;

        $this->assertSame(
            '404 Not Found',
            $exception->getHttpStatus()
        );
    }
}
