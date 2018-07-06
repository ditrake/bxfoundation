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
    public function getHeaders()
    {
        $return = $this->headers;

        $sendedHeaders = $this->getSentHeaders();
        foreach ($sendedHeaders as $header) {
            list($name, $value) = array_map('trim', explode(':', $header));
            $return[$name] = $value;
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        $allHeaders = $this->getHeaders();

        return isset($allHeaders[$name]) ? $allHeaders[$name] : null;
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

    /**
     * возвращает те заголовки, которые уже были отправлены.
     *
     * @return array
     */
    protected function getSentHeaders()
    {
        return headers_list();
    }
}
