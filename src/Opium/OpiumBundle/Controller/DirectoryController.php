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
        return $this->getDirectoryAction('/');
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
        $path = urldecode($path);
        $path = $this->getPath($path);
        $fileList = [];

        //$files = $this->get('opium.finder.photo')
        //    ->find($path);

        return $this->get('opium.finder.photo')->get($path);
            //'files' => $files,
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
    public function putDirectoryAction($path, Request $request)
    {
        $directory = $this->get('jms_serializer')->deserialize($request->getContent(), 'Opium\OpiumBundle\Entity\Directory', 'json');

        if ($directory->getDirectoryThumbnail()) {
            $dir = $this->container->getParameter('thumbs_directory') . $directory->getPathname();
            file_put_contents($dir . '/config.yml', 'photo: ' . $directory->getDirectoryThumbnail()->getPathname());
        }

        return $this->get('opium.finder.photo')->get($directory->getPathname());
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
