<?php

namespace marvin255\bxfoundation\response;

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
     * @return \marvin255\bxfoundation\response\ResponseInterface
     */
    public function setHeader($name, $value);

    /**
     * Возвращает список всех установленных заголовков.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Возращает заголовок ответа по названию.
     *
     * @param string $name Название заголовка
     *
     * @return string|null
     */
    public function getHeader($name);

    /**
     * Задает новый статус ответа.
     *
     * @param string $status
     *
     * @return \marvin255\bxfoundation\response\ResponseInterface
     */
    public function setStatus($status);

    /**
     * Возвращает текущий статус ответа.
     *
     * @return int
     */
    public function getStatus();
}
