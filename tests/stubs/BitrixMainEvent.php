<?php

namespace Bitrix\Main;

/**
 * Мок для Bitrix\Main\Event.
 */
class Event
{
    /**
     * @var callable
     */
    public static $sendCallback;
    /**
     * @var string
     */
    protected $module;
    /**
     * @var string
     */
    protected $event;
    /**
     * @var array
     */
    protected $params = [];
    /**
     * @var array
     */
    protected $results = [];

    /**
     * @param string $module
     * @param string $event
     * @param array  $params
     */
    public function __construct($module, $event, array $params)
    {
        $this->module = $module;
        $this->event = $event;
        $this->params = $params;
    }

    /**
     * Событие на исполнение.
     */
    public function send()
    {
        if (is_callable(self::$sendCallback)) {
            $this->results = call_user_func_array(self::$sendCallback, [
                $this->module,
                $this->event,
                $this->params,
            ]);
        }
    }

    /**
     * Возвращает результат обработки события.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }
}

/**
 * Мок для Bitrix\Main\EventResult.
 */
class EventResult
{
    /**
     * @var string
     */
    const ERROR = 'ERROR';
    /**
     * @var string
     */
    const UNDEFINED = 'UNDEFINED';
    /**
     * @var string
     */
    protected $type;
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param string $type
     * @param array  $params
     */
    public function __construct($type, array $params)
    {
        $this->type = $type;
        $this->params = $params;
    }

    /**
     * Возвращает тип результата.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Возвращает параметры результата.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
