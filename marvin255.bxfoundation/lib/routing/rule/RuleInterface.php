<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\routing\filter\FilterInterface;

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
     * @return \marvin255\bxfoundation\routing\rule\RuleResultInterface|null
     */
    public function parse(RequestInterface $request, RuleResultInterface $ruleResult = null);

    /**
     * Создает ссылку на основании указанных параметров.
     *
     * @param array $params Параметры для создания ссылки
     *
     * @return string
     */
    public function createUrl(array $params = []);

    /**
     * Добавляет фильтр к правилу.
     *
     * @param \marvin255\bxfoundation\routing\filter\FilterInterface $filter
     *
     * @return self
     */
    public function filter(FilterInterface $filter);
}
