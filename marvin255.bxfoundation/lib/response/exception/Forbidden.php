<?php

namespace marvin255\bxfoundation\response\exception;

use marvin255\bxfoundation\response\HttpStatus;

/**
 * Доступ запрещен.
 */
class Forbidden extends Response
{
    /**
     * @inheritdoc
     */
    public function getHttpStatus()
    {
        return HttpStatus::FORBIDDEN;
    }
}
