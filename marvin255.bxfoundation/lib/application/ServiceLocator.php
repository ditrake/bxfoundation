<?php

namespace marvin255\bxfoundation\application;

/**
 * Простейшая реализация паттерна Service Locator для 1С-Битрикс.
 */
class ServiceLocator implements ServiceLocatorInterface
{
    /**
     * Массив с сервисами, которые установлены для данного локатора.
     * Массив вида `название сервиса => объект`.
     *
     * @var array
     */
    protected $services = [];

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\application\Exception
     */
    public function set($name, $service)
    {
        $name = $this->convertName($name);
        if ($name === '') {
            throw new Exception('Empty name for service');
        }
        if (!is_object($service)) {
            throw new Exception("{$name} service handler must be an object");
        }
        $this->services[$name] = $service;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\application\Exception
     */
    public function get($name)
    {
        $name = $this->convertName($name);
        if (!isset($this->services[$name])) {
            throw new Exception("{$name} service doesn't exist");
        }

        return $this->services[$name];
    }

    /**
     * @inheritdoc
     */
    public function has($name)
    {
        $name = $this->convertName($name);

        return isset($this->services[$name]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\application\Exception
     */
    public function delete($name)
    {
        $name = $this->convertName($name);
        if (!isset($this->services[$name])) {
            throw new Exception("{$name} service doesn't exist");
        }

        unset($this->services[$name]);

        return $this;
    }

    /**
     * Магия. Возвращает установленный сервис.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Магия. Задает сервис по имени.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * Магия. Проверяет, что сервис существует.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Магия. Удаляет сервис по его имени.
     *
     * @param string $name
     */
    public function __unset($name)
    {
        $this->delete($name);
    }

    /**
     * Преобразовывает название сервиса к единообразному виду.
     *
     * @param string $name
     *
     * @return string
     */
    protected function convertName($name)
    {
        return strtolower(trim($name));
    }
}
