<?php

namespace marvin255\bxfoundation\services\cache;

/**
 * Интерфейс для объектов кэширования библиотеки.
 */
interface CacheInterface
{
    /**
     * Сохраняет данные в кэше.
     *
     * @param string $key      Ключ кэша
     * @param mixed  $data     Данные для кэширования
     * @param int    $duration Время, на которое нужно кэшировать
     * @param array  $tags     Теги, от которых зависит кэш
     *
     * @return \marvin255\bxfoundation\services\cache\CacheInterface
     */
    public function set($key, $data, $duration = null, array $tags = null);

    /**
     * Возвращает данные, сохраненные по ключу, либо false, если данных нет.
     *
     * @param string $key Ключ кэша
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Очищает кэшированные данные по ключу.
     *
     * @param string $key
     *
     * @return \marvin255\bxfoundation\services\cache\CacheInterface
     */
    public function clear($key);
}
