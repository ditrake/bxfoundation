<?php

namespace creative\foundation\routing;

/**
 * Класс для исключений, связанных с http ответом.
 */
class HttpException extends Exception
{
    /**
     * Код ответа.
     *
     * @var int
     */
    protected $httpCode = null;

    /**
     * @inheritdoc
     */
    public function __construct($message = 'Internal Server Error', $code = 500, \Exception $previous = null)
    {
        $this->httpCode = (int) $code ?: $this->httpCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Возврщает код ответа.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Возвращает полную строку статуса.
     *
     * @return string
     */
    public function getHttpStatus()
    {
        return $this->getHttpCode() . ' ' . $this->getMessage();
    }
}
