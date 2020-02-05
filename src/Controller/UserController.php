<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @param $id
     * @param SerializerInterface $serializer
     * @return JsonResponse
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
     * @return JsonResponse
     * @Route("/user/delete/{id}", methods={"DELETE"})
     */
    public function deleteUser($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
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
        $user->setUsername($userData->getUsername());
        $user->setMail($userData->getMail());
        $entityManager->flush();

        return new JsonResponse($user, 201);
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

        return new JsonResponse($user, 201);
    }





}
