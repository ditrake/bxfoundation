<?php

namespace marvin255\bxfoundation\tests\lib\events;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\events\Result;
use marvin255\bxfoundation\events\EventableTrait;
use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\events\Exception;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * Класс с интерфейсом и трейэтом для проверки трейта.
 */
class EventableObject implements EventableInterface
{
    use EventableTrait;
}

/**
 * Класс теста.
 */
class EventableTraitTest extends BaseCase
{
    /**
     * @test
     */
    public function testAttachEventWrongNameException()
    {
        $eventable = new EventableObject;

        $this->setExpectedException(Exception::class);
        $eventable->attachEventCallback(false, function ($result) {});
    }

    /**
     * @test
     */
    public function testAttachEventWrongCallabckException()
    {
        $eventable = new EventableObject;

        $this->setExpectedException(Exception::class);
        $eventable->attachEventCallback('event', 123);
    }

    /**
     * @test
     */
    public function testRiseEvent()
    {
        $eventName = 'event_name_' . mt_rand();
        $eventParam = 'event_value_' . mt_rand();

        $eventable = new EventableObject;
        $eventResult = new Result($eventName, $eventable, []);

        $eventable->attachEventCallback($eventName, function ($result) use ($eventParam) {
            $result->setParam('event_param', $eventParam);
            $result->fail();
        });

        $eventable->riseEvent($eventResult);

        $this->assertSame($eventParam, $eventResult->getParam('event_param'));
        $this->assertSame(false, $eventResult->isSuccess());
    }

    /**
     * @test
     */
    public function testRiseEventBitrix()
    {
        $eventName = 'event_name_' . mt_rand();
        $eventParam = 'event_value_' . mt_rand();
        $eventParamBitrix = 'event_value_bitrix_' . mt_rand();

        $eventable = new EventableObject;
        $eventResult = new Result($eventName, $eventable, []);

        Event::$sendCallback = function ($module, $event, $params) use ($eventName, $eventParam, $eventParamBitrix) {
            $return = [];
            if (
                $module === 'marvin255.bxfoundation'
                && $event === $eventName
                && $params['event_param'] === $eventParam
            ) {
                $return[] = new EventResult(
                    EventResult::ERROR,
                    ['bitrix_param' => $eventParamBitrix]
                );
            }

            return $return;
        };
        $eventable->attachEventCallback($eventName, function ($result) use ($eventParam) {
            $result->setParam('event_param', $eventParam);
        });

        $eventable->riseEvent($eventResult);

        $this->assertSame(null, $eventResult->getParam('event_param'));
        $this->assertSame($eventParamBitrix, $eventResult->getParam('bitrix_param'));
        $this->assertSame(false, $eventResult->isSuccess());
    }
}
