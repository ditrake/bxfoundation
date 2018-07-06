<?php

namespace marvin255\bxfoundation\tests\lib\services\user;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\services\user\Bitrix;
use marvin255\bxfoundation\services\Exception;

class BitrixTest extends BaseCase
{
    /**
     * @test
     */
    public function testGetId()
    {
        $id = mt_rand();

        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['getId'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $user = new Bitrix($bxUser);

        $this->assertSame($id, $user->getId());
    }

    /**
     * @test
     */
    public function testIsAuthorized()
    {
        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['isAuthorized'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('isAuthorized')
            ->will($this->returnValue(false));

        $user = new Bitrix($bxUser);

        $this->assertSame(false, $user->isAuthorized());
    }

    /**
     * @test
     */
    public function testIsAdmin()
    {
        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['isAdmin'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(true));

        $user = new Bitrix($bxUser);

        $this->assertSame(true, $user->isAdmin());
    }

    /**
     * @test
     */
    public function testAuthorize()
    {
        $id = mt_rand();
        $remember = false;

        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['authorize'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('authorize')
            ->with($this->equalTo($id), $this->equalTo($remember))
            ->will($this->returnValue(false));

        $user = new Bitrix($bxUser);

        $this->assertSame(false, $user->authorize($id, $remember));
    }

    /**
     * @test
     */
    public function testLogin()
    {
        $login = 'login_' . mt_rand();
        $password = 'password_' . mt_rand();
        $remember = false;

        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['login'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('login')
            ->with(
                $this->equalTo($login),
                $this->equalTo($password),
                $this->equalTo($remember ? 'Y' : 'N'),
                $this->equalTo('Y')
            )
            ->will($this->returnValue(['error']));

        $user = new Bitrix($bxUser);

        $this->assertSame(false, $user->login($login, $password, $remember));
    }

    /**
     * @test
     */
    public function testGlobalUser()
    {
        $id = mt_rand();

        global $USER;
        $USER = $this->getMockBuilder('\CUser')
            ->setMethods(['getId'])
            ->getMock();
        $USER->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $user = new Bitrix;

        $this->assertSame($id, $user->getId());
    }

    /**
     * @test
     */
    public function testGlobalUserException()
    {
        $user = new Bitrix;

        $this->setExpectedException(Exception::class);
        $user->getId();
    }
}
