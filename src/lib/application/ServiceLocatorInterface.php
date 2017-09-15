<?php

namespace marvin255\bxfoundation\application;

/**
 * Простейшая реализация паттерна Service Locator для 1С-Битрикс.
 */
interface ServiceLocatorInterface
{
    /**
     * Задает новый сервис под указанным именем.
     *
     * @param string $name
     * @param object $service
     *
     * @return \marvin255\bxfoundation\application\ServiceLocatorInterface
     */
    public function set($name, $service);

    /**
     * Возвращает объект сервиса по его имени.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Проверяет существует ли сервис с указанным именем.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Удаляет сервис по его имени.
     *
     * @param string $name
     *
     * @return \marvin255\bxfoundation\application\ServiceLocatorInterface
     */
    public function delete($name);
}
