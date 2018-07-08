<?php

namespace marvin255\bxfoundation\response;

/**
 * Хэлпер для работы с http статусами.
 */
class HttpStatus
{
    /**
     * Константы для отдельных статусов.
     */
    const OK = 200;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const FAILED_DEPENDENCY = 424;
    const SERVER_ERROR = 500;
    /**
     * @var array
     */
    protected static $statusMessages = [
        200 => '200 OK',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        500 => '500 Internal Server Error',
    ];

    /**
     * Возвращает сообщение по коду статуса.
     *
     * @param int $statusCode
     *
     * @return string
     */
    public static function getMessageByCode($statusCode)
    {
        return isset(self::$statusMessages[$statusCode])
            ? self::$statusMessages[$statusCode]
            : '';
    }

    /**
     * Провряет можно ли задать существующий статус.
     *
     * @param int $statusCode
     *
     * @return bool
     */
    public static function isStatusAcceptable($statusCode)
    {
        return isset(self::$statusMessages[$statusCode]);
    }
}
