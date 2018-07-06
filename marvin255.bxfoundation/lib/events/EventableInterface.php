<?php

namespace marvin255\bxfoundation\events;

/**
 * Интерфейс для объекта, у которого есть события.
 */
interface EventableInterface
{
    /**
     * Добавляет обработчик события к указанному событию.
     *
     * @param string         $eventName
     * @param array|callable $callback
     */
    public function attachEventCallback($eventName, $callback);

    /**
     * Вызывает обработку события.
     *
     * @param \marvin255\bxfoundation\events\ResultInterface $result
     *
     * @return \marvin255\bxfoundation\events\ResultInterface
     */
    public function riseEvent(ResultInterface $result);
}
