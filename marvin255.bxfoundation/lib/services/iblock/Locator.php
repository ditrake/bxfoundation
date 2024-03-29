<?php

namespace marvin255\bxfoundation\services\iblock;

use marvin255\bxfoundation\services\cache\CacheInterface;
use Bitrix\Main\Loader;
use marvin255\bxfoundation\services\Exception;

/**
 * Сервис для быстрого поиска данных об инфоблоках.
 */
class Locator
{
    /**
     * Массив с фильтрами для поиска инфоблоков.
     *
     * @var array
     */
    protected $filter = [];
    /**
     * Поля инфоблока, которые необходимо получить.
     *
     * @var array
     */
    protected $select = [];
    /**
     * Ссылка на объект кэша.
     *
     * @var \marvin255\bxfoundation\services\cache\CacheInterface|null
     */
    protected $cache = null;
    /**
     * Список инфоблоков для данного локатора.
     *
     * @var array
     */
    protected $list = null;

    /**
     * Конструктор.
     *
     * @param array                                                 $filter Массив с фильтрами для поиска инфоблоков
     * @param array                                                 $select Поля инфоблока, которые необходимо получить
     * @param \marvin255\bxfoundation\services\cache\CacheInterface $cache  Ссылка на объект кэша
     *
     * @throws \marvin255\bxfoundation\services\Exception
     */
    public function __construct($filter = null, $select = null, CacheInterface $cache = null)
    {
        if ($filter !== null && !is_array($filter)) {
            throw new Exception('Wrong filter parameter type');
        }
        if ($filter === null) {
            $this->filter = [
                'ACTIVE' => 'Y',
                'CHECK_PERMISSIONS' => 'N',
            ];
            if (defined('SITE_ID') && (!defined('ADMIN_SECTION') || !ADMIN_SECTION)) {
                $this->filter['SITE_ID'] = SITE_ID;
            }
        } else {
            $this->filter = $filter;
        }

        if ($select !== null && !is_array($select)) {
            throw new Exception('Wrong select parameter type');
        }
        if ($select === null) {
            $this->select = [
                'ID',
                'CODE',
                'NAME',
                'LID',
                'IBLOCK_TYPE_ID',
                'DETAIL_PAGE_URL',
                'SECTION_PAGE_URL',
                'LIST_PAGE_URL',
                'PROPERTIES',
            ];
        } else {
            $this->select = array_unique(array_merge($select, ['ID']));
        }

        $this->cache = $cache;
    }

    /**
     * Возвращает идентификатор инфоблока по его коду.
     *
     * @param string $code Код инфоблока
     *
     * @return string
     */
    public function getIdByCode($code)
    {
        return $this->findBy('CODE', $code, 'ID');
    }

    /**
     * Возвращает код инфоблока по его идентификатору.
     *
     * @param string $id
     *
     * @return string
     */
    public function getCodeById($id)
    {
        return $this->findBy('ID', $id, 'CODE');
    }

    /**
     * Ищет инфоблок по значению указанного поля.
     *
     * @param string $field  Поле, по значениям которого будет производиться поиск
     * @param mixed  $value  Значение для поиска
     * @param string $select Поле инфоблока, которое нужно вывести в ответе, если не указано, то выводятся все поля инфоблока
     *
     * @return array|string|null
     */
    public function findBy($field, $value, $select = null)
    {
        $list = $this->findAllBy($field, $value, $select);
        $return = null;
        if ($list) {
            $return = reset($list);
        }

        return $return;
    }

    /**
     * Ищет список инфоблоков по значению указанного поля.
     *
     * @param string $field  Поле, по значениям которого будет производиться поиск
     * @param mixed  $value  Значение для поиска
     * @param string $select Поле инфоблока, которое нужно вывести в ответе, если не указано, то выводятся все поля инфоблока
     *
     * @return array
     */
    public function findAllBy($field, $value, $select = null)
    {
        $return = null;
        $list = $this->getList();
        foreach ($list as $iblock) {
            if (isset($iblock[$field]) && $iblock[$field] == $value) {
                if ($select) {
                    $return[] = isset($iblock[$select]) ? $iblock[$select] : null;
                } else {
                    $return[] = $iblock;
                }
            }
        }

        return $return;
    }

    /**
     * Возвращает список инфоблоков для данного локатора.
     *
     * @return array
     */
    public function getList()
    {
        if ($this->list === null) {
            $cid = get_class($this) . json_encode(array_merge($this->filter, $this->select));
            if (!$this->cache || ($list = $this->cache->get($cid)) === false) {
                $list = $this->loadList($this->filter, $this->select);
                $arCacheTags = array_reduce($list, function ($carry, $item) {
                    $carry[] = "iblock_id_{$item['ID']}";

                    return $carry;
                }, ['iblock_id_new']);
                if ($this->cache) {
                    $this->cache->set($cid, $list, null, $arCacheTags);
                }
            }
            $this->list = $list;
        }

        return $this->list;
    }

    /**
     * Загружает список инфоблоков согласно условия и списка полей.
     *
     * @param array $filter Условия для поиска инфоблоков
     * @param array $select Список полей для загрузки
     *
     * @return array
     *
     * @throws \marvin255\bxfoundation\services\Exception
     */
    protected function loadList(array $filter, array $select)
    {
        $return = [];

        Loader::includeModule('iblock');
        $res = \CIblock::GetList([], $filter);
        while ($ob = $res->Fetch()) {
            $arItem = [];
            foreach ($select as $field) {
                if (!isset($ob[$field])) {
                    continue;
                }
                $arItem[$field] = $ob[$field];
            }
            $return[$ob['ID']] = $arItem;
        }
        if (in_array('PROPERTIES', $select) && !empty($return)) {
            $pRes = \CIBlockProperty::GetList(['sort' => 'asc'], []);
            while ($pOb = $pRes->Fetch()) {
                if (!isset($return[$pOb['IBLOCK_ID']])) {
                    continue;
                }
                if (empty($pOb['CODE'])) {
                    throw new Exception("Property '{$pOb['NAME']}'({$pOb['ID']}) has no code");
                }
                $return[$pOb['IBLOCK_ID']]['PROPERTIES'][$pOb['CODE']] = $pOb;
            }
        }

        return array_values($return);
    }
}
