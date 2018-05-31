<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * getMeAction
     *
     * @Route("/me", name="get_me")
     */
    public function getMeAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = [
            'type' => 'user',
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ];

        return new JsonResponse($data);
    }
}

