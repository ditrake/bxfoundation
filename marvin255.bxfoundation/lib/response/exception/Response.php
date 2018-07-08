<?php

namespace marvin255\bxfoundation\response\exception;

use marvin255\bxfoundation\response\HttpStatus;

/**
 * Базовое исключение для http ответов, от которого унаследуются все остальные.
 */
abstract class Response extends \marvin255\bxfoundation\Exception
{
    /**
     * Возвращает код http-статуса исключения.
     *
     * @return int
     */
    abstract public function getHttpStatus();

    /**
     * Возвращает код http-статуса исключения.
     *
     * @return int
     */
    public function getHttpStatusMessage()
    {
        return HttpStatus::getMessageByCode($this->getHttpStatus());
    }
}
