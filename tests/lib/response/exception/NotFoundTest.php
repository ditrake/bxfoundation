<?php

namespace marvin255\bxfoundation\tests\lib\response\exception;

use marvin255\bxfoundation\response\exception\NotFound;
use marvin255\bxfoundation\response\HttpStatus;

class NotFoundTest extends ResponseCase
{
    /**
     * @test
     */
    public function testStatus()
    {
        $this->assertResponse(new NotFound, HttpStatus::NOT_FOUND);
    }
}
