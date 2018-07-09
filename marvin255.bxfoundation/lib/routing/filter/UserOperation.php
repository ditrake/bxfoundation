<?php

namespace marvin255\bxfoundation\routing\filter;

use marvin255\bxfoundation\events\ResultInterface;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\services\user\UserInterface;

/**
 * Фильтр по доступным для пользователя операциям.
 *
 * Проверяет, чтобы пользователь мог выполнять хотя бы одну операцию из
 * указанного списка.
 */
class UserOperation implements FilterInterface
{
    /**
     * Текущий пользователь.
     *
     * @var marvin255\bxfoundation\services\user\UserInterface
     */
    protected $user;
    /**
     * Список операций.
     *
     * @var array
     */
    protected $operations = [];

    /**
     * Конструктор.
     *
     * @param \marvin255\bxfoundation\services\user\UserInterface $user       Объект с текущим пользователем
     * @param array|string                                        $operations Массив с операциями или строка, которые проходят фильтр
     */
    public function __construct(UserInterface $user, $operations)
    {
        $this->user = $user;

        if (empty($operations)) {
            throw new Exception(
                'Operations parameter must a string or an array of bitrix users operations'
            );
        }
        $operations = is_array($operations) ? $operations : [$operations];
        $this->operations = $operations;
    }

    /**
     * @inheritdoc
     */
    public function attachTo(EventableInterface $route)
    {
        $route->attachEventCallback('onAfterRouteParsing', [$this, 'filter']);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filter(ResultInterface $eventResult)
    {
        foreach ($this->operations as $operation) {
            if ($this->user->canDoOperation($operation)) {
                continue;
            }
            $eventResult->fail();
            break;
        }
    }
}
