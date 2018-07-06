<?php

namespace marvin255\bxfoundation\events;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * Трэйт, который реализует в себе систему событий для объекта.
 */
trait EventableTrait
{
    /**
     * @var array
     */
    protected $events = [];

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\events\Exception
     */
    public function attachEventCallback($eventName, $callback)
    {
        $eventName = $this->prepareEventName($eventName);

        if ($eventName === '') {
            throw new Exception("Event name can't be empty");
        }

        if (
            !is_callable($callback)
            && (!is_array($callback) || empty($callback[0]) || empty($callback[1]))
        ) {
            throw new Exception(
                "Callback param for {$eventName} event"
                . ' must be a callble or an array instance ready for call_user_func'
            );
        }

        $this->events[$eventName][] = $callback;
    }

    /**
     * {@inheritdoc}
     *
     * В данную реализацию добавлен проксирование вызова соответствующего
     * события из 1C-Битрикс. События из 1C-Битрикс вызываются после событий,
     * привязанных к данному объекту.
     */
    public function riseEvent(ResultInterface $result)
    {
        $eventName = $this->prepareEventName($result->getName());

        $this->riseInternalEvents($eventName, $result);
        if (class_exists('\Bitrix\Main\Event') && $result->isSuccess()) {
            $this->riseBitrixEvents($eventName, $result);
        }

        return $result;
    }

    /**
     * Запускает на выполнение события, привязанные к данному объекту.
     *
     * @param string                                         $eventName
     * @param \marvin255\bxfoundation\events\ResultInterface $result
     */
    protected function riseInternalEvents($eventName, ResultInterface $result)
    {
        $callbacks = !empty($this->events[$eventName])
            ? $this->events[$eventName]
            : [];

        foreach ($callbacks as $callback) {
            call_user_func_array($callback, [$result]);
            if (!$result->isSuccess()) {
                break;
            }
        }
    }

    /**
     * Запускает на выполнение события, объявленные через механизмы 1C-Битрикс.
     *
     * @param string                                         $eventName
     * @param \marvin255\bxfoundation\events\ResultInterface $result
     */
    protected function riseBitrixEvents($eventName, ResultInterface $result)
    {
        $event = new Event('marvin255.bxfoundation', $eventName, $result->getParams());
        $event->send();
        foreach ($event->getResults() as $eventResult) {
            $result->setParams($eventResult->getParams());
            $eventResultType = $eventResult->getType();
            if ($eventResultType === EventResult::ERROR || $eventResultType === EventResult::UNDEFINED) {
                $result->fail();
                break;
            }
        }
    }

    /**
     * Обрабатывает название события для использования внутри методов.
     *
     * @param string $eventName
     *
     * @return string
     */
    protected function prepareEventName($eventName)
    {
        return strtolower(trim($eventName));
    }
}
