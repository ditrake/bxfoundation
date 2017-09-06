<?php

namespace creative\foundation\routing\action;

use creative\foundation\request\RequestInterface;
use creative\foundation\response\ResponseInterface;
use creative\foundation\routing\rule\RuleResultInterface;

/**
 * Действие, которое должно быть выполнено для укзанного правила.
 */
interface ActionInterface
{
    /**
     * Запуск действия на выполнение.
     *
     * Возвращает строку, в которой содержится html ответа.
     *
     * @param \creative\foundation\routing\rule\RuleResultInterface $ruleResult Ссылка на объект с параметрами, полученными от обработчика url
     * @param \creative\foundation\request\RequestInterface         $request    Ссылка на текущий объект запроса
     * @param \creative\foundation\response\ResponseInterface         $response    Ссылка на текущий объект запроса
     *
     * @return string
     */
    public function run(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response);
}
