<?php

namespace creative\foundation\events;

/**
 * Интерфейс для объекта, у которого есть события.
 */
interface EventableInterface
{
    /**
     * Вызывает обработку события.
     *
     * @param \creative\foundation\events\ResultInterface $result
     *
     * @return \creative\foundation\events\ResultInterface
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
