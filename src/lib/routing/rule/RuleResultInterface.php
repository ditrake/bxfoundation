<?php

namespace marvin255\bxfoundation\routing\rule;

/**
 * Интерфейс для объекта, в котором хранится результат,
 * который возвращает правило после успешной обработки url.
 */
interface RuleResultInterface
{
    /**
     * Конструктор.
     *
     * Задает первоначальные параметры.
     *
     * @param array $params
     */
    public function __construct(array $params = array());

    /**
     * Задает список всех параметров ответа.
     *
     * @param array $params Массив вида "название параметра => значение" для дополнительных параметров ответа
     *
     * @return \marvin255\bxfoundation\routing\rule\RuleResultInterface
     */
    public function setParams(array $params);

    /**
     * Возвращает список всех параметров ответа.
     *
     * @return array
     */
    public function getParams();

    /**
     * Задает параметр ответа по его имени.
     *
     * @param string $name  Имя параметра
     * @param mixed  $value Значение параметра
     *
     * @return \marvin255\bxfoundation\routing\rule\RuleResultInterface
     */
    public function setParam($name, $value);

    /**
     * Возвращает параметр ответа по его имени.
     *
     * @param string $name Имя параметра
     *
     * @return mixed
     */
    public function getParam($name);
}
