<?php

namespace  Bitrix\Main\Config;

/**
 * Мок для Bitrix\Main\Config\Option.
 */
class Option
{
    /**
     * Массив опций для тестов.
     *
     * @var array
     */
    public static $settedOptions = [];

    /**
     * Возвращает значение указанной опции.
     *
     * @param string      $moduleId Идентификатор модуля, внутри которого нужно искать опцию
     * @param string      $name     Название опции
     * @param string      $default  Значение по умолчанию, которое будет возвращаено, если опция не будет найлена
     * @param bool|string $siteId   Идентификатор сайта, для которого нужно искать опцию
     *
     * @return mixed
     */
    public static function get($moduleId, $name, $default = null, $siteId = false)
    {
        return isset(self::$settedOptions[$moduleId][$name])
            ? self::$settedOptions[$moduleId][$name]
            : $default;
    }

    /**
     * Задает значение указанной опции.
     *
     * @param string $moduleId Идентификатор модуля, внутри которого нужно искать опцию
     * @param string $name     Название опции
     * @param string $value    Новое значение опции
     * @param string $siteId   Идентификатор сайта, для которого нужно искать опцию
     */
    public static function set($moduleId, $name, $value, $siteId = false)
    {
        self::$settedOptions[$moduleId][$name] = $value;
    }
}
