<?php

namespace Opium\OpiumBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    /**
     * getMeAction
     *
     * @access public
     * @return void
     *
     * @ApiDoc(
     *  resource=true,
     *  section="User",
     *  description="Get the current user"
     * )
     * @Rest\Get("/me", name="get_me", options={ "method_prefix" = false })
     */
    public function getMeAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ];

        $view = $this->view($data);

        return $this->handleView($view);
    }
}
