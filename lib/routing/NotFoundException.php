<?php

namespace creative\foundation\routing;

/**
 * Класс для исключений, для не найденных ресурсов.
 */
class NotFoundException extends HttpException
{
    /**
     * @inheritdoc
     */
    public function __construct($message = 'Not Found', $code = 404, \Exception $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getHttpStatus()
    {
        return $this->getHttpCode() . ' Not Found';
    }
}
