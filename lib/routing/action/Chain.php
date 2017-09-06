<?php

namespace creative\foundation\routing\action;

use creative\foundation\request\RequestInterface;
use creative\foundation\routing\rule\RuleResultInterface;
use creative\foundation\routing\Exception;

/**
 * Цепочка из нескольки последовательно выполняющихся действий.
 */
class Chain implements ActionInterface
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
     * @param array $actions Список действий, которые нужно выполнить по цепочке.
     *
     * @throws \creative\foundation\routing\Exception
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
    public function run(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response)
    {
        foreach ($this->actions as $action) {
            $action->run($ruleResult, $request, $response);
        }
    }
}
