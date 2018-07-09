<?php

namespace marvin255\bxfoundation\routing\action;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;

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
     * @param \marvin255\bxfoundation\routing\rule\RuleResultInterface $ruleResult Ссылка на объект с параметрами, полученными от обработчика url
     * @param \marvin255\bxfoundation\request\RequestInterface         $request    Ссылка на текущий объект запроса
     * @param \marvin255\bxfoundation\response\ResponseInterface       $response   Ссылка на текущий объект ответа
     *
     * @return string
     */
    public function run(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response);
}
