<?php

namespace marvin255\bxfoundation\events;

/**
 * Объект, который представляет результат обработки события.
 *
 * Хранит в себе результат обработки события и параметры события, один и тот же
 * экземпляр результатов передается по цепочке от одного обработчика события
 * к другому. Соответственно, любой обработчик может использовать любой параметр
 * события и выставить результат.
 */
interface ResultInterface
{
    /**
     * Конструктор.
     *
     * Задает начальные параметры события, для которого данный объект служит
     * результатом.
     *
     * @param string $name   Имя события
     * @param mixed  $target Ссылка на объект, который инициировал событие
     * @param array  $params Массив вида "название параметра => значение" для дополнительных параметров события
     */
    public function __construct($name, $target, array $params = array());

    /**
     * Возвращает имя события, для которого был создан результат.
     *
     * @return string
     */
    public function getName();

    /**
     * Возвращает текущий статус события.
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Устанавливает статус, что событие провалено.
     *
     * @return \marvin255\bxfoundation\events\ResultInterface
     */
    public function fail();

    /**
     * Задает список всех параметров данного события.
     *
     * @param array $params Массив вида "название параметра => значение" для дополнительных параметров события
     *
     * @return \marvin255\bxfoundation\events\ResultInterface
     */
    public function setParams(array $params);

    /**
     * Возвращает список всех параметров данного события.
     *
     * @return array
     */
    public function getParams();

    /**
     * Задает параметр события по его имени.
     *
     * @param string $name  Имя параметра
     * @param mixed  $value Значение параметра
     *
     * @return \marvin255\bxfoundation\events\ResultInterface
     */
    public function setParam($name, $value);

    /**
     * Возвращает параметр события по его имени.
     *
     * @param string $name Имя параметра
     *
     * @return mixed
     */
    public function getParam($name);

    /**
     * Возвращает ссылку на объект, который инициировал событие.
     *
     * @return mixed
     */
    public function getTarget();
}
