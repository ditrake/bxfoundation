<?php

namespace marvin255\bxfoundation\tests\lib\response\exception;

use marvin255\bxfoundation\response\exception\BadRequest;
use marvin255\bxfoundation\response\HttpStatus;

class BadRequestTest extends ResponseCase
{
    /**
     * @test
     */
    public function testStatus()
    {
        $this->assertResponse(new BadRequest, HttpStatus::BAD_REQUEST);
    }
}
