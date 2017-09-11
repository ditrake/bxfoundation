<?php

namespace creative\foundation\routing\filter;

use creative\foundation\routing\Exception;
use Bitrix\Main\GroupTable;
use creative\foundation\events\EventableInterface;
use creative\foundation\events\ResultInterface;

/**
 * Фильтр по группам пользователя.
 *
 * Проверяет, чтобы пользователь принадлежал хотябы к одной группе из списка.
 */
class UserGroup implements FilterInterface
{
    /**
     * Список групп.
     *
     * @var array
     */
    protected $groups = [];
    /**
     * Флаг, который обозначает, что администратору доступ разрешен в любом случае.
     *
     * @var bool
     */
    protected $allowedToAdmin = true;

    /**
     * Конструктор.
     *
     * @param array|string $groups         Массив с группами или строка, которые проходят фильтр
     * @param bool         $allowedToAdmin Флаг, который обозначает, что администратору доступ разрешен в любом случае
     *
     * @throws \creative\foundation\routing\Exception
     */
    public function __construct($groups, $allowedToAdmin = true)
    {
        if (empty($groups)) {
            throw new Exception('Constructor parameter can\'t be empty');
        }
        $this->groups = is_array($groups) ? $groups : [$groups];
        $this->allowedToAdmin = $allowedToAdmin;
    }

    /**
     * @inheritdoc
     */
    public function attachTo(EventableInterface $route)
    {
        $route->attachEventCallback('onAfterRouteParsing', [
            $this,
            'filter',
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \creative\foundation\routing\Exception
     */
    public function filter(ResultInterface $eventResult)
    {
        global $USER;

        $groups = [];
        foreach ($this->groups as $groupKey => $group) {
            $id = null;
            foreach (static::getGroups() as $siteGroup) {
                if (
                    (int) $siteGroup['ID'] !== (int) $group
                    && $siteGroup['STRING_ID'] !== $group
                ) {
                    continue;
                }
                $id = (int) $siteGroup['ID'];
                break;
            }
            if (!$id) {
                throw new Exception("Wrong group identity {$group} with key {$groupKey}");
            }
            $groups[] = $id;
        }

        if (!$USER->isAuthorized()) {
            $eventResult->fail();
        } elseif (!$this->allowedToAdmin || !$USER->isAdmin()) {
            $userGroups = array_map('intval', $USER->GetUserGroupArray());
            if (!array_intersect($groups, $userGroups)) {
                $eventResult->fail();
            }
        }
    }

    /**
     * Список кэшированных групп для данного фильтра.
     *
     * Получаем одним запросом все группы и сохраняем в статическую переменную,
     * которой сможет пользоваться каждый инстанс данного фильтра.
     *
     * @var array
     */
    protected static $loadedGroups = null;

    /**
     * Возвращает список всех доступных на сайте групп.
     *
     * Получает группы при первом же запросе и кэширует список
     * в статическую переменную, при каждом последующем обращении
     * возвращает данные из этой переменной.
     *
     * @return array
     */
    protected static function getGroups()
    {
        if (static::$loadedGroups === null) {
            static::$loadedGroups = GroupTable::getList()->fetchAll();
        }

        return static::$loadedGroups;
    }
}
