<?php

namespace marvin255\bxfoundation\tests\lib\response\exception;

use marvin255\bxfoundation\response\exception\ServerError;
use marvin255\bxfoundation\response\HttpStatus;

class ServerErrorTest extends ResponseCase
{
    /**
     * @test
     */
    public function testStatus()
    {
        $this->assertResponse(new ServerError, HttpStatus::SERVER_ERROR);
    }
}
