<?php

namespace marvin255\bxfoundation\tests\lib\routing;

class HttpExceptionTest extends \marvin255\bxfoundation\tests\BaseCase
{
    public function testGetHttpStatus()
    {
        $exception = new \marvin255\bxfoundation\routing\HttpException;

        $this->assertSame(
            '500 Internal Server Error',
            $exception->getHttpStatus()
        );
    }
}
