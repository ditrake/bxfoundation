<?php

namespace creative\foundation\routing\filter;

use creative\foundation\routing\Exception;
use creative\foundation\events\ResultInterface;
use creative\foundation\events\EventableInterface;

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
            throw new Exception('Constructor parameter can\'t be empty');
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
        if (!$request || !in_array($request->getMethod(), $this->methods, true)) {
            $eventResult->fail();
        }
    }
}
