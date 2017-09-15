<?php

namespace marvin255\bxfoundation\response;

/**
 * Обертка над битриксовым классом ответа для того, чтобы добавить к нему интерфейс.
 *
 * @see \Bitrix\Main\HttpResponse
 */
class Bitrix implements ResponseInterface
{
    /**
     * Объект ответа, который инициируется битриксом.
     *
     * @var \Bitrix\Main\HttpRequest
     */
    protected $bitrixResponse = null;
    /**
     * Текущий статус.
     *
     * @var string
     */
    protected $status = '200 OK';
    /**
     * Список установленных заголовков.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * @param \Bitrix\Main\HttpResponse $response
     */
    public function __construct(\Bitrix\Main\HttpResponse $response)
    {
        $this->bitrixResponse = $response;
    }

    /**
     * @inheritdoc
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        $this->bitrixResponse->addHeader($name, $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->bitrixResponse->setStatus($status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }
}
