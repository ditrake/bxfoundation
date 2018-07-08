<?php

namespace marvin255\bxfoundation\response\exception;

use marvin255\bxfoundation\response\HttpStatus;

/**
 * Неверный запрос.
 */
class BadRequest extends Response
{
    /**
     * @inheritdoc
     */
    public function getHttpStatus()
    {
        return HttpStatus::BAD_REQUEST;
    }
}
