<?php

namespace App\Tests;

use App\Controller\UserController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;

class UserControllerTest extends TestCase
{
    //Working
    public function testCreateUser()
    {
        $request = new Request();
        $user = new User();
        $res = '';

        $userManager = $this->useMock(EntityManager::class, array(), array());
        $doctrine = $this->useMock(UserController::class, array('getManager'), array(array($userManager)));
        $serialize = $this->useMock(SerializerInterface::class, array(), array($res));

        $controller = $this->useMock(UserController::class,
            array('getDoctrine'),
            array(array($doctrine))
        );

        $result = $controller->createUser($request,$serialize);
        $this->assertEquals(201, $result->getStatusCode());
        $this->assertEquals($serialize->serialize($user,'json'), $result->getContent());
    }

    //Working

    public function testUpdateUser()
    {
        $user = new User();
        $id = 35;
        $user->setUsername("nameEdited");
        $user->setMail("MailEdited@mail.com");
        $request = new Request();
        $res = '';

        $find = $this->useMock(UserRepository::class, array(), array(array($user)));
        $userRepository = $this->useMock(User::class, array('find'), array(array($find)));
        $userManager = $this->useMock(EntityManager::class, array('getRepository'), array(array($userRepository)));
        $doctrine = $this->useMock(UserController::class, array('getManager'), array(array($userManager)));
        $serialize = $this->useMock(SerializerInterface::class, array(), array($res));

        $controller = $this->useMock(UserController::class,
            array('getDoctrine'),
            array(array($doctrine))
        );

        $result = $controller->updateUser($id,$request,$serialize);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals($serialize->serialize($user,'json'), $result->getContent());
    }

    /*public function testDeleteUser()
    {
        $user = new User();
        $id = 35;

        $find = $this->useMock(UserRepository::class, array(), array(array($user)));
        $userRepository = $this->useMock(User::class, array('find'), array(array($find)));
        $userManager = $this->useMock(EntityManager::class, array('getRepository'), array(array($userRepository)));
        $doctrine = $this->useMock(UserController::class, array('getManager'), array(array($userManager)));

        $controller = $this->useMock(UserController::class,
            array('getDoctrine'),
            array(array($doctrine))
        );

        $result = $controller->deleteUser($id);
        $this->assertEquals(200, $result->getStatusCode());
    }*/

    //Working
    public function testReadUser()
    {
        $user = new User();
        $id = 20;
        $res = '';

        $find = $this->useMock(UserRepository::class, array(), array(array($user)));
        $userRepository = $this->useMock(User::class, array('find'), array(array($find)));
        $userManager = $this->useMock(EntityManager::class, array('getRepository'), array(array($userRepository)));
        $doctrine = $this->useMock(UserController::class, array('getManager'), array(array($userManager)));
        $serialize = $this->useMock(SerializerInterface::class, array(), array($res));

        $controller = $this->useMock(UserController::class,
            array('getDoctrine'),
            array(array($doctrine))
        );

        $result = $controller->readUser($id, $serialize);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals($serialize->serialize($user,'json'), $result->getContent());
    }

    //Working
    public function testReadAllUser()
    {
        $users[] = new User();
        $res = '';

        $findAll = $this->useMock(UserRepository::class, array(), array(array($users)));
        $userRepository = $this->useMock(User::class, array('findAll'), array(array($findAll)));
        $userManager = $this->useMock(EntityManager::class, array('getRepository'), array(array($userRepository)));
        $doctrine = $this->useMock(UserController::class, array('getManager'), array(array($userManager)));
        $serialize = $this->useMock(SerializerInterface::class, array(), array($res));

        $controller = $this->useMock(UserController::class,
            array('getDoctrine'),
            array(array($doctrine))
        );

        $result = $controller->readAllUser($serialize);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals($serialize->serialize($users,'json'), $result->getContent());
    }


    private function useMock($class, $methods, $returns)
    {
        if (count($methods) == 0) {
            $mock = $this->getMockBuilder($class)
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $mock = $this->getMockBuilder($class)
                ->setMethods($methods)
                ->disableOriginalConstructor()
                ->getMock();

            foreach ($methods as $k => $method) {
                if (count($returns[$k]) == 0) {
                    // rien
                } elseif (count($returns[$k]) == 1) {
                    $mock->expects($this->any())
                        ->method($method)
                        ->willReturn($returns[$k][0]);
                } else {
                    $mock->expects($this->any())
                        ->method($method)
                        ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $returns[$k]));
                }
            }
        }
        return $mock;

    }
}