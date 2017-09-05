<?php

namespace creative\foundation\routing\rule;

use creative\foundation\request\RequestInterface;

/**
 * Интерфейс для правила перехода от url к действию.
 */
interface RuleInterface
{
    /**
     * Проверяет подходит ли текущий запрос под данное правило.
     * В случае успеха возваращает параметры, которые правило получило из запроса.
     * В случае неудачи false.
     *
     * @param \creative\foundation\request\RequestInterface         $request
     * @param \creative\foundation\routing\rule\RuleResultInterface $ruleResult
     *
     * @return null|\creative\foundation\routing\rule\RuleResultInterface
     */
    public function parse(RequestInterface $request, RuleResultInterface $ruleResult = null);
}
