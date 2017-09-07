<?php

namespace creative\foundation\response;

/**
 * Интерфейс для описания данных для ответа.
 */
interface ResponseInterface
{
    /**
     * Устанавливает новый заголовок ответа или заменяет старый.
     *
     * @param string $name  Название заголовка
     * @param string $value Значение заголовка
     *
     * @return \creative\foundation\response\ResponseInterface
     */
    public function setHeader($name, $value);

    /**
     * Возращает заголовок ответа по названию.
     *
     * @param string $name Название заголовка
     *
     * @return string
     */
    public function getHeader($name);

    /**
     * Задает новый статус ответа.
     *
     * @param string $status
     *
     * @return \creative\foundation\response\ResponseInterface
     */
    public function setStatus($status);

    /**
     * Возвращает текущий статус ответа.
     *
     * @return int
     */
    public function getStatus();
}
