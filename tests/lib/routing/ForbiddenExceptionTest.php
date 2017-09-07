<?php

namespace creative\foundation\tests\lib\routing;

class ForbiddenExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHttpStatus()
    {
        $exception = new \creative\foundation\routing\ForbiddenException;

        $this->assertSame(
            '403 Forbidden',
            $exception->getHttpStatus()
        );
    }
}
