<?php

namespace Opium\OpiumBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class DirectoryController extends FOSRestController
{

    /**
     * getDirectoriesAction
     *
     * @access public
     *
     * @ApiDoc()
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function getDirectoriesAction()
    {
        return $this->get('opium.repository.directory')->findOneBySlug('');
    }

    /**
     * getDirectoryAction
     *
     * @param mixed $path
     * @access public
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @ApiDoc(
     *     description="Get the selected directory information",
     *     requirements={
     *         {"name"="path", "dataType"="string", "description"="Wanted path"}
     *     }
     * )
     */
    public function getDirectoryAction($path)
    {
        $dir = $this->get('opium.repository.directory')->findOneBySlug($path);
        return $dir;
    }

    /**
     * Update a directory
     *
     * @param mixed $path
     * @access public
     * @return void
     *
     * @ApiDoc(description="Update a directory")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function putDirectoryAction($id, Request $request)
    {
        // update only the directory thumbnail for now
        $post = $request->request->all();
        $coverId = $post['_embedded']['directory_thumbnail']['id'];

        $photo = $this->get('opium.repository.photo')->find($coverId);
        $directory = $this->get('opium.repository.directory')->find($id);

        //$directory = $this->get('jms_serializer')->deserialize($request->getContent(), 'Opium\OpiumBundle\Entity\Directory', 'json');
        $directory->setDirectoryThumbnail($photo);

        $em = $this->get('doctrine')->getManager();
        //$em->merge($directory);
        $em->flush();
        return $directory;
    }

    /**
     * getPath
     *
     * @param string $path
     * @access private
     * @return string
     */
    private function getPath($path)
    {
        // TODO fix for this https://github.com/angular/angular.js/pull/7940
        $path = str_replace('_slash_', '/', $path);
        if (substr($path, -2) == '//') {
            $path = substr($path, 0, -1);
        }

        return $path;
    }
}
