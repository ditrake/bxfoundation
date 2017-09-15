<?php

namespace marvin255\bxfoundation\events;

/**
 * Объект, который представляет результат обработки события.
 *
 * Хранит в себе результат обработки события и параметры события, один и тот же
 * экземпляр результатов передается по цепочке от одного обработчика события
 * к другому. Соответственно, любой обработчик может использовать любой параметр
 * события и выставить результат.
 */
class Result implements ResultInterface
{
    /**
     * Имя события, для которого предоставлен данный объект.
     *
     * @var string
     */
    protected $name = null;
    /**
     * Ссылка на объект-инициатор, который выбросил данное событие.
     *
     * @var mixed
     */
    protected $target = null;
    /**
     * Параметры события.
     *
     * @var array
     */
    protected $params = [];
    /**
     * Текущий результат выполнения события.
     *
     * @var bool
     */
    protected $eventResult = true;

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\events\Exception
     */
    public function __construct($name, $target, array $params = array())
    {
        if (empty($name)) {
            throw new Exception('Event result name can\'t be empty');
        }
        $this->name = $name;
        if (!is_object($target)) {
            throw new Exception('Event target must be an object instance');
        }
        $this->target = $target;
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function isSuccess()
    {
        return $this->eventResult;
    }

    /**
     * @inheritdoc
     */
    public function fail()
    {
        $this->eventResult = false;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setParams(array $params)
    {
        $this->params = [];
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @inheritdoc
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function getTarget()
    {
        return $this->target;
    }
}
