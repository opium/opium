<?php

namespace Opium\OpiumBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class FileController extends FOSRestController
{
    /**
     * photoAction
     *
     * @param string $path
     * @access public
     * @return Response
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @ApiDoc()
     */
    public function getFileAction($slug)
    {
        $file = $this->get('opium.repository.photo')->findOneBySlug($slug);
        $neighbour = $file->getParent()->getChildren();
        $index = $neighbour->indexOf($file);

        $previous = $neighbour[$index - 1];
        $next = $neighbour[$index + 1];

        $file->setPrevious($previous)
            ->setNext($next);

        return $file;
    }

    /**
     * Update a file
     *
     * @param int $id
     * @param Request $request
     * @access public
     * @return void
     *
     * @ApiDoc(description="Update a file")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function putFileAction($id, Request $request)
    {
        // update only the directory thumbnail for now
        $post = $request->request->all();

        $photo = $this->get('opium.repository.photo')->find($id);

        if (!empty($post['position']['lat'])) {
            $photo->setLatitude($post['position']['lat']);
        }
        if (!empty($post['position']['lat'])) {
            $photo->setLongitude($post['position']['lng']);
        }

        $em = $this->get('doctrine')->getManager()->flush();

        return $photo;
    }
}
