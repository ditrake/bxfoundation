<?php

namespace creative\foundation\routing\filter;

use creative\foundation\routing\Exception;
use creative\foundation\events\ResultInterface;
use creative\foundation\events\EventableInterface;

/**
 * Фильтр по заголовкам запроса.
 *
 * Проверяет, чтобы в текущем запросе были бы соответствующие заголовки,
 * установленые в соответствующие значения.
 */
class Header implements FilterInterface
{
    /**
     * Массив заголовков и их значений, которые проходят фильтр.
     *
     * @var @array
     */
    protected $headers = [];

    /**
     * Конструктор.
     *
     * @param array $headers Массив заголовков и их значений, которые проходят фильтр
     */
    public function __construct(array $headers)
    {
        if (empty($headers)) {
            throw new Exception('Constructor parameter can\'t be empty');
        }
        $this->headers = $headers;
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

        foreach ($this->headers as $headerName => $headerValue) {
            $requestHeaderValue = $request->getHeader($headerName);
            if ($requestHeaderValue !== $headerValue) {
                $eventResult->fail();
                break;
            }
        }
    }
}
