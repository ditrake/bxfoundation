<?php

namespace marvin255\bxfoundation\tests\lib\response\exception;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\response\exception\Response;

abstract class ResponseCase extends BaseCase
{
    /**
     * Проверяет, что код исключения верен и, что исключение вернет сообщение.
     *
     * @param \marvin255\bxfoundation\response\exception\Response $exception
     * @param int                                                 $code
     */
    protected function assertResponse(Response $exception, $code)
    {
        $this->assertSame($code, $exception->getHttpStatus());
        $this->assertNotEmpty($exception->getHttpStatusMessage());
        $this->assertInternalType('string', $exception->getHttpStatusMessage());
    }
}
