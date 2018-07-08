<?php

namespace marvin255\bxfoundation\response\exception;

use marvin255\bxfoundation\response\HttpStatus;

/**
 * Ошибка во время выполнения на сервере.
 */
class ServerError extends Response
{
    /**
     * @inheritdoc
     */
    public function getHttpStatus()
    {
        return HttpStatus::SERVER_ERROR;
    }
}
