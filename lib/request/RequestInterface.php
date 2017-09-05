<?php

namespace creative\foundation\request;

/**
 * Интерфейс для описания данных текущего запроса.
 */
interface RequestInterface
{
    /**
     * Возвращает метод запроса.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Возвращает протокол запроса.
     *
     * @return string
     */
    public function getScheme();

    /**
     * Возвращает имя виртуального хоста запроса.
     *
     * @return string
     */
    public function getHost();

    /**
     * Возвращает путь к файлу на сервере.
     *
     * @return string
     */
    public function getPath();

    /**
     * Возвращает параметры запроса.
     *
     * @return array
     */
    public function getData();

    /**
     * Возвращает заголовки запроса в формате название => значение.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Возвращает заголовок запроса по его имени.
     *
     * @param string $name
     *
     * @return string
     */
    public function getHeader($name);

    /**
     * Возвращает куки запроса в формате название => значение.
     *
     * @return array
     */
    public function getCookie();

    /**
     * Возвращает ip адрес пользователя.
     *
     * @return string
     */
    public function getRemoteAddress();
}
