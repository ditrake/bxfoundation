<?php

namespace marvin255\bxfoundation\routing;

/**
 * Класс для исключений, с недостаточными правами доступа.
 */
class ForbiddenException extends HttpException
{
    /**
     * @inheritdoc
     */
    public function __construct($message = 'Forbidden', $code = 403, \Exception $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}