<?php

namespace creative\foundation\routing\router;

use creative\foundation\routing\rule\RuleInterface;
use creative\foundation\routing\action\ActionInterface;
use creative\foundation\request\RequestInterface;
use creative\foundation\response\ResponseInterface;

/**
 * Интерфейс для объекта, который ищет подходящее правило для url
 * и отображает связанное с ним действие.
 */
interface RouterInterface
{
    /**
     * Регистрирует соответствие между правилом и действием.
     *
     * @param \creative\foundation\routing\rule\RuleInterface     $rule   Ссылка на правило
     * @param \creative\foundation\routing\action\ActionInterface $action Ссылка на действие
     *
     * @return \creative\foundation\routing\router\RouterInterface
     */
    public function registerRoute(RuleInterface $rule, ActionInterface $action);

    /**
     * Регистрирует соответствие между правилом и действием для исключительной ситуации.
     *
     * @param int                                                 $code   Код http ответа
     * @param \creative\foundation\routing\action\ActionInterface $action Ссылка на действие
     *
     * @return \creative\foundation\routing\router\RouterInterface
     */
    public function registerRouteException($code, ActionInterface $action);

    /**
     * Ищет правило, под которое подходит текущий запрос и выполняет
     * ассоциированное с ним действие.
     *
     * @param \creative\foundation\request\RequestInterface   $request  Ссылка на текущий объект запроса
     * @param \creative\foundation\response\ResponseInterface $response Ссылка на текущий объект ответа
     *
     * @return string
     */
    public function route(RequestInterface $request, ResponseInterface $response);
}
