<?php

namespace creative\foundation\services\user;

/**
 * Интерфейс для объекта, в котором хранится текущий пользователь.
 */
interface UserInterface
{
    /**
     * Возвращает идентификатор текущего пользователя.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Проверяет авторизован ли пользователь.
     *
     * @return bool
     */
    public function isAuthorized();

    /**
     * Проверяет принадлежность пользователя группе администраторов.
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Метод непосредственно осуществляет процесс авторизации пользователя.
     *
     * @param int  $id       Идентификатор пользователя, которого нужно авторизовать
     * @param bool $remember Флаг для запоминания авторизации
     *
     * @return bool
     */
    public function authorize($id, $rememder = false);

    /**
     * Метод проверяет логин и пароль и если они корректные, то авторизует пользователя.
     *
     * @param string $login    Логин пользователя
     * @param string $password Пароль
     * @param bool   $remember Флаг для запоминания авторизации
     *
     * @return bool
     */
    public function login($login, $password, $rememder = false);
}
