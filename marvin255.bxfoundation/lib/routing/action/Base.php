<?php

namespace marvin255\bxfoundation\routing\action;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use marvin255\bxfoundation\events\Result;
use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\events\EventableTrait;

/**
 * Абстрактный класс для действия.
 */
abstract class Base implements ActionInterface, EventableInterface
{
    use EventableTrait;

    /**
     * Выполнение действия данным компонентом.
     *
     * @param \marvin255\bxfoundation\routing\rule\RuleResultInterface $ruleResult Ссылка на объект с параметрами, полученными от обработчика url
     * @param \marvin255\bxfoundation\request\RequestInterface         $request    Ссылка на текущий объект запроса
     * @param \marvin255\bxfoundation\response\ResponseInterface       $response   Ссылка на текущий объект ответа
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
            if ($onAfterActionRun->isSuccess()) {
                $return = $onAfterActionRun->getParam('return');
            }
        }

        return $return;
    }
}
