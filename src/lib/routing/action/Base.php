<?php

namespace creative\foundation\routing\action;

use creative\foundation\request\RequestInterface;
use creative\foundation\response\ResponseInterface;
use creative\foundation\routing\rule\RuleResultInterface;
use creative\foundation\events\Result;
use creative\foundation\events\EventableInterface;
use creative\foundation\events\EventableTrait;

/**
 * Абстрактный класс для действия.
 */
abstract class Base implements ActionInterface, EventableInterface
{
    use EventableTrait;

    /**
     * Выполнение действия данным компонентом.
     *
     * @param \creative\foundation\routing\rule\RuleResultInterface $ruleResult Ссылка на объект с параметрами, полученными от обработчика url
     * @param \creative\foundation\request\RequestInterface         $request    Ссылка на текущий объект запроса
     * @param \creative\foundation\response\ResponseInterface       $response   Ссылка на текущий объект запроса
     *
     * @return string
     */
    abstract protected function runInternal(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response);

    /**
     * @inheritdoc
     */
    public function run(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response)
    {
        $return = null;
        $onBeforeActionRun = new Result(
            'onBeforeActionRun',
            $this,
            [
                'ruleResult' => $ruleResult,
                'request' => $request,
                'response' => $response,
            ]
        );
        $this->riseEvent($onBeforeActionRun);
        if ($onBeforeActionRun->isSuccess()) {
            $return = $this->runInternal($ruleResult, $request, $response);
            $onAfterActionRun = new Result(
                'onAfterActionRun',
                $this,
                [
                    'ruleResult' => $ruleResult,
                    'request' => $request,
                    'response' => $response,
                    'return' => $return,
                ]
            );
            $this->riseEvent($onAfterActionRun);
            $return = $onAfterActionRun->getParam('return');
        }

        return $return;
    }
}
