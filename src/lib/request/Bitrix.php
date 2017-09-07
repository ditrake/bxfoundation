<?php

namespace creative\foundation\request;

/**
 * Обертка над битриксовым классом запроса для того, чтобы добавить к нему интерфейс.
 *
 * @see \Bitrix\Main\HttpRequest
 */
class Bitrix implements RequestInterface
{
    /**
     * @var \Bitrix\Main\HttpRequest
     */
    protected $bitrixRequest = null;

    /**
     * @param \Bitrix\Main\HttpRequest $request
     */
    public function __construct(\Bitrix\Main\HttpRequest $request)
    {
        $this->bitrixRequest = $request;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->bitrixRequest->getRequestMethod();
    }

    /**
     * @inheritdoc
     */
    public function getScheme()
    {
        return $this->bitrixRequest->isHttps() ? 'https' : 'http';
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->bitrixRequest->getHttpHost();
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return parse_url($this->bitrixRequest->getRequestUri(), PHP_URL_PATH);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $return = [];
        $method = $this->getMethod();
        if ($method === 'GET') {
            $return = $this->bitrixRequest->getQueryList()->toArray();
        } elseif ($method === 'POST') {
            $return = $this->bitrixRequest->getPostList()->toArray();
        } else {
            parse_str($this->getPhpInputData(), $return);
        }

        return $return;
    }

    /**
     * Возвращает данные из входящего потока php.
     *
     * @return string
     */
    protected function getPhpInputData()
    {
        return file_get_contents('php://input');
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        $return = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') !== 0) {
                continue;
            }
            $return[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        $headers = $this->getHeaders();

        return isset($headers[$name]) ? $headers[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function getCookie()
    {
        return $this->bitrixRequest->getCookieList()->toArray();
    }

    /**
     * @inheritdoc
     */
    public function getRemoteAddress()
    {
        return $this->bitrixRequest->getRemoteAddress();
    }
}
