<?php

namespace marvin255\bxfoundation\services\user;

use CUser;
use marvin255\bxfoundation\services\Exception;

/**
 * Объект, в котором хранится текущий пользователь.
 *
 * Если в конструкторе не был указан объект пользователя, использует global $USER.
 * В противном случает использует объект пользователя из конструктора.
 */
class Bitrix implements UserInterface
{
    /**
     * Объект пользователя из битрикса.
     *
     * @var \CUser
     */
    protected $user;

    /**
     * Конструктор.
     *
     * @param CUser $user Объект пользователя из битрикса
     */
    public function __construct(CUser $user = null)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        $id = $this->getUser()->getId();

        return $id ? (int) $id : null;
    }

    /**
     * @inheritdoc
     */
    public function isAuthorized()
    {
        return $this->getUser()->isAuthorized();
    }

    /**
     * @inheritdoc
     */
    public function isAdmin()
    {
        return $this->getUser()->isAdmin();
    }

    /**
     * @inheritdoc
     */
    public function authorize($id, $remember = false)
    {
        return $this->getUser()->authorize($id, $remember);
    }

    /**
     * @inheritdoc
     */
    public function login($login, $password, $remember = false)
    {
        $remember = $remember ? 'Y' : 'N';
        $res = $this->getUser()->login($login, $password, $remember, 'Y');

        return $res === true;
    }

    /**
     * Возвращает текущего пользователя битрикса.
     *
     * @return \CUser
     *
     * @throws \marvin255\bxfoundation\services\Exception
     */
    protected function getUser()
    {
        $user = null;
        if ($this->user) {
            $user = $this->user;
        } else {
            global $USER;
            if (empty($USER) || !($USER instanceof CUser)) {
                throw new Exception("Can't find user object in bitrix");
            }
            $user = $USER;
        }

        return $user;
    }
}
