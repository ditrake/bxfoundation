<?php

namespace creative\foundation\tests\lib\services\user;

class BitrixTest extends \PHPUnit_Framework_TestCase
{
    public function testGetId()
    {
        $id = mt_rand();

        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['getId'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $user = new \creative\foundation\services\user\Bitrix($bxUser);

        $this->assertSame(
            $id,
            $user->getId()
        );
    }

    public function testIsAuthorized()
    {
        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['isAuthorized'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('isAuthorized')
            ->will($this->returnValue(false));

        $user = new \creative\foundation\services\user\Bitrix($bxUser);

        $this->assertSame(
            false,
            $user->isAuthorized()
        );
    }

    public function testIsAdmin()
    {
        $bxUser = $this->getMockBuilder('\CUser')
            ->setMethods(['isAdmin'])
            ->getMock();
        $bxUser->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(true));

        $user = new \creative\foundation\services\user\Bitrix($bxUser);

        $this->assertSame(
            true,
            $user->isAdmin()
        );
    }

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

        $user = new \creative\foundation\services\user\Bitrix($bxUser);

        $this->assertSame(
            false,
            $user->authorize($id, $remember)
        );
    }

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

        $user = new \creative\foundation\services\user\Bitrix($bxUser);

        $this->assertSame(
            false,
            $user->login($login, $password, $remember)
        );
    }

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

        $user = new \creative\foundation\services\user\Bitrix;

        $this->assertSame(
            $id,
            $user->getId()
        );
    }

    public function testGlobalUserException()
    {
        $user = new \creative\foundation\services\user\Bitrix;
        $this->setExpectedException('\creative\foundation\services\Exception');
        $user->getId();
    }
}
