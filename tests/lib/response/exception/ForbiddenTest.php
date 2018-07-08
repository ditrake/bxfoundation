<?php

namespace marvin255\bxfoundation\tests\lib\response\exception;

use marvin255\bxfoundation\response\exception\Forbidden;
use marvin255\bxfoundation\response\HttpStatus;

class ForbiddenTest extends ResponseCase
{
    /**
     * @test
     */
    public function testStatus()
    {
        $this->assertResponse(new Forbidden, HttpStatus::FORBIDDEN);
    }
}
