<?php

namespace marvin255\bxfoundation\events;

/**
 * Интерфейс для объекта, у которого есть события.
 */
interface EventableInterface
{
    /**
     * Вызывает обработку события.
     *
     * @param \marvin255\bxfoundation\events\ResultInterface $result
     *
     * @return \marvin255\bxfoundation\events\ResultInterface
     */
    public function riseEvent(ResultInterface $result);

    /**
     * Добавляет обработчик события к указанному событию.
     *
     * @param string         $eventName
     * @param array|callable $callback
     */
    public function attachEventCallback($eventName, $callback);

    /**
     * Удаляет обработчик события из события.
     *
     * @param string         $eventName
     * @param array|callable $callback
     */
    public function detachEventCallback($eventName, $callback);
}
