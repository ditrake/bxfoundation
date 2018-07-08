<?php

namespace marvin255\bxfoundation\tests\lib\response\exception;

use marvin255\bxfoundation\response\exception\Unauthorized;
use marvin255\bxfoundation\response\HttpStatus;

class UnauthorizedTest extends ResponseCase
{
    /**
     * @test
     */
    public function testStatus()
    {
        $this->assertResponse(new Unauthorized, HttpStatus::UNAUTHORIZED);
    }
}
