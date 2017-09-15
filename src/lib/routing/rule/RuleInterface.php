<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\request\RequestInterface;

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
     * @param \marvin255\bxfoundation\request\RequestInterface         $request
     * @param \marvin255\bxfoundation\routing\rule\RuleResultInterface $ruleResult
     *
     * @return null|\marvin255\bxfoundation\routing\rule\RuleResultInterface
     */
    public function parse(RequestInterface $request, RuleResultInterface $ruleResult = null);
}
