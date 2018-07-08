<?php

namespace marvin255\bxfoundation\tests\lib\response;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\response\HttpStatus;

class HttpStatusTest extends BaseCase
{
    /**
     * @test
     */
    public function testGetMessageByCode()
    {
        $this->assertSame(
            '404 Not Found',
            HttpStatus::getMessageByCode(HttpStatus::NOT_FOUND)
        );
    }

    /**
     * @test
     */
    public function testIsStatusAcceptable()
    {
        $this->assertSame(
            true,
            HttpStatus::isStatusAcceptable(HttpStatus::NOT_FOUND)
        );
        $this->assertSame(
            false,
            HttpStatus::isStatusAcceptable(111111)
        );
    }
}
