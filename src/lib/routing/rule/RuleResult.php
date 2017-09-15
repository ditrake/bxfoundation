<?php

namespace marvin255\bxfoundation\routing\rule;

/**
 * Объект, в котором хранится результат,
 * который возвращает правило после успешной обработки url.
 */
class RuleResult implements RuleResultInterface
{
    /**
     * Параметры ответа.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Конструктор.
     *
     * Задает первоначальные параметры.
     *
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        if ($params) {
            $this->setParams($params);
        }
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
}
