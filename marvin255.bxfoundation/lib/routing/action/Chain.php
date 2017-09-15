<?php

namespace marvin255\bxfoundation\routing\action;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use marvin255\bxfoundation\routing\Exception;

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
        if (empty($actions)) {
            throw new Exception('Actions list can\'t be empty');
        }
        foreach ($actions as $key => $action) {
            if ($action instanceof ActionInterface) {
                continue;
            }
            throw new Exception("Action with key {$key} is not an ActionInterface instance");
        }
        $this->actions = $actions;
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
