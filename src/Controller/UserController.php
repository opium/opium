<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends Controller
{
    /**
     * getMeAction
     *
     * @Route("/me", name="get_me")
     */
    public function getMeAction(SerializerInterface $serializer)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return new Response($serializer->serialize($user, 'json'));
    }
}

