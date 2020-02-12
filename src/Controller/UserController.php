<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @param $id
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/user/{id}", methods={"GET"})
     */
    public function readUser($id, SerializerInterface $serializer) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $res = $serializer->serialize($user, 'json');
        return new Response($res, 200);
    }

    /**
     * @param $id
     * @return Response
     * @Route("/user/delete/{id}", methods={"DELETE"})
     */
    public function deleteUser($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($user != null) {
            $em->remove($user);
            $em->flush();
        }


        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Route("/")
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function readAllUser(SerializerInterface $serializer) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();

        $res = $serializer->serialize($users, 'json');
        return new Response($res, 200);
    }

    /**
     * @param $id
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("user/edit/{id}", methods={"PUT"})
     */
    public function updateUser($id, Request $request, SerializerInterface $serializer) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $data = $request->getContent();
        $userData = $serializer->deserialize($data, User::class, 'json');
        if ($userData != null) {
            if($userData->getUsername()!= null) {
            $user->setUsername($userData->getUsername());
            }
            if($userData->getMail()!= null) {
                $user->setMail($userData->getMail());
            }
            $entityManager->flush();
        }

        $data = $serializer->serialize($user, 'json');
        return new Response($data, 200);
    }

    /**
     * @Route("/user", methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function createUser(Request $request, SerializerInterface $serializer) {
        $em = $this->getDoctrine()->getManager();

        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');

        $em->persist($user);
        $em->flush();

        $data = $serializer->serialize($user,'json');

        return new Response($data, 201);
    }





}
