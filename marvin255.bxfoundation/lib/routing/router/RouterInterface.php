<?php

namespace marvin255\bxfoundation\routing\router;

use marvin255\bxfoundation\routing\rule\RuleInterface;
use marvin255\bxfoundation\routing\action\ActionInterface;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;

/**
 * Интерфейс для объекта, который ищет подходящее правило для url
 * и отображает связанное с ним действие.
 */
interface RouterInterface
{
    /**
     * Регистрирует соответствие между правилом и действием.
     *
     * @param \marvin255\bxfoundation\routing\rule\RuleInterface     $rule   Ссылка на правило
     * @param \marvin255\bxfoundation\routing\action\ActionInterface $action Ссылка на действие
     *
     * @return \marvin255\bxfoundation\routing\router\RouterInterface
     */
    public function registerRoute(RuleInterface $rule, ActionInterface $action);

    /**
     * Регистрирует соответствие между правилом и действием для исключительной ситуации.
     *
     * @param int                                                    $code   Код http ответа
     * @param \marvin255\bxfoundation\routing\action\ActionInterface $action Ссылка на действие
     *
     * @return \marvin255\bxfoundation\routing\router\RouterInterface
     */
    public function registerExceptionAction($code, ActionInterface $action);

    /**
     * Ищет правило, под которое подходит текущий запрос и выполняет
     * ассоциированное с ним действие.
     *
     * @param \marvin255\bxfoundation\request\RequestInterface   $request  Ссылка на текущий объект запроса
     * @param \marvin255\bxfoundation\response\ResponseInterface $response Ссылка на текущий объект ответа
     *
     * @return string
     */
    public function route(RequestInterface $request, ResponseInterface $response);
}
