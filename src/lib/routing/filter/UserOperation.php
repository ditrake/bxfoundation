<?php

namespace creative\foundation\routing\filter;

use creative\foundation\events\ResultInterface;
use creative\foundation\routing\Exception;
use creative\foundation\events\EventableInterface;

/**
 * Фильтр по доступным для пользователя операциям.
 *
 * Проверяет, чтобы пользователь мог выполнять хотя бы одну операцию из
 * указанного списка.
 */
class UserOperation implements FilterInterface
{
    /**
     * Список операций.
     *
     * @var array
     */
    protected $operations = [];
    /**
     * Флаг, который обозначает, что администратору доступ разрешен в любом случае.
     *
     * @var bool
     */
    protected $allowedToAdmin = true;

    /**
     * Конструктор.
     *
     * @param array|string $operations     Массив с операциями или строка, которые проходят фильтр
     * @param bool         $allowedToAdmin Флаг, который обозначает, что администратору доступ разрешен в любом случае
     */
    public function __construct($operations, $allowedToAdmin = true)
    {
        if (empty($operations)) {
            throw new Exception('Constructor parameter can\'t be empty');
        }
        $operations = is_array($operations) ? $operations : [$operations];
        $this->operations = $operations;
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
     * @inheritdoc
     */
    public function filter(ResultInterface $eventResult)
    {
        global $USER;
        if (!$USER->isAuthorized()) {
            $eventResult->fail();
        } elseif (!$this->allowedToAdmin || !$USER->isAdmin()) {
            $userForTests = new \CUser;
            foreach ($this->operations as $operation) {
                if ($userForTests->CanDoOperation($operation, $USER->getId())) {
                    continue;
                }
                $eventResult->fail();
                break;
            }
        }
    }
}
