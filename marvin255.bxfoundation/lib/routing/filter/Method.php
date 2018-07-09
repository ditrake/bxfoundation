<?php

namespace marvin255\bxfoundation\routing\filter;

use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\events\ResultInterface;
use marvin255\bxfoundation\events\EventableInterface;

/**
 * Фильтр по методу запроса.
 */
class Method implements FilterInterface
{
    /**
     * Массив методов, которые проходят фильтр.
     *
     * @var @array
     */
    protected $methods = [];

    /**
     * Конструктор.
     *
     * @param array|string $method Массив с методами или строка, которые проходят фильтр
     */
    public function __construct($method)
    {
        if (empty($method)) {
            throw new Exception(
                'Method parameter must be a string or array of valid http methods'
            );
        }
        $method = is_array($method) ? $method : [$method];
        $this->methods = array_map('mb_strtoupper', $method);
    }

    /**
     * @inheritdoc
     */
    public function attachTo(EventableInterface $route)
    {
        $route->attachEventCallback('onBeforeRouteParsing', [
            $this,
            'filter',
        ]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filter(ResultInterface $eventResult)
    {
        $request = $eventResult->getParam('request');
        if (!in_array($request->getMethod(), $this->methods, true)) {
            $eventResult->fail();
        }
    }
}
