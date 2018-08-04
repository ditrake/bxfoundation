<?php

namespace marvin255\bxfoundation\request;

/**
 * Класс, который копирует данные из некого базового запроса и позволяет
 * подменить часть данных.
 *
 * В основном требуется для подмены инстанса запроса во время события.
 */
class Replacement implements RequestInterface
{
    /**
     * Базовый запрос.
     */
    protected $request;
    /**
     * Данные для подмены.
     *
     * @var array
     */
    protected $replacements = [];

    /**
     * @param \marvin255\bxfoundation\request\RequestInterface $request
     * @param array                                            $replacements
     */
    public function __construct(RequestInterface $request, array $replacements = [])
    {
        $this->request = $request;
        $this->replacements = $replacements;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->getReplacementOrCallMethodFromRequest('method');
    }

    /**
     * @inheritdoc
     */
    public function getScheme()
    {
        return $this->getReplacementOrCallMethodFromRequest('scheme');
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->getReplacementOrCallMethodFromRequest('host');
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->getReplacementOrCallMethodFromRequest('path');
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->getReplacementOrCallMethodFromRequest('data');
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->getReplacementOrCallMethodFromRequest('headers');
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
        return $this->getReplacementOrCallMethodFromRequest('cookie');
    }

    /**
     * @inheritdoc
     */
    public function getRemoteAddress()
    {
        return $this->getReplacementOrCallMethodFromRequest('remoteAddress');
    }

    /**
     * Если есть подмена, то возвращает подмену, в противном случае возвращает
     * данные из базового запроса.
     *
     * @param string $param
     *
     * @return mixed
     */
    protected function getReplacementOrCallMethodFromRequest($param)
    {
        $getter = 'get' . ucfirst($param);

        $return = null;
        if (isset($this->replacements[$param])) {
            $return = $this->replacements[$param];
        } elseif (method_exists($this->request, $getter)) {
            $return = $this->request->$getter();
        }

        return $return;
    }
}
