<?php

namespace marvin255\bxfoundation\routing\action;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;

/**
 * Цепочка из нескольки последовательно выполняющихся действий.
 */
class Chain extends Base
{
    /**
     * Список действий, которые нужно выполнить по цепочке.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Конструктор.
     *
     * @param array $actions Список действий, которые нужно выполнить по цепочке
     *
     * @throws \marvin255\bxfoundation\routing\Exception
     */
    public function __construct(array $actions)
    {
        foreach ($actions as $action) {
            $this->chain($action);
        }
    }

    /**
     * Добавляет действие в цепочку.
     *
     * @param \marvin255\bxfoundation\routing\action\ActionInterface $action
     *
     * @return self
     */
    public function chain(ActionInterface $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function runInternal(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response)
    {
        $return = null;
        foreach ($this->actions as $action) {
            $return .= $action->run($ruleResult, $request, $response);
        }

        return $return;
    }
}
