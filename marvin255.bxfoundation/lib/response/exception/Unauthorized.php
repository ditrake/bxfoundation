<?php

namespace marvin255\bxfoundation\response\exception;

use marvin255\bxfoundation\response\HttpStatus;

/**
 * Страница не найдена.
 */
class Unauthorized extends Response
{
    /**
     * @inheritdoc
     */
    public function getHttpStatus()
    {
        return HttpStatus::UNAUTHORIZED;
    }
}
