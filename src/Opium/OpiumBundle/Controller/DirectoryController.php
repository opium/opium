<?php

namespace Opium\OpiumBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Finder\Finder;

class DirectoryController extends FOSRestController
{
    /**
     * indexAction
     *
     * @access public
     * @return void
     *
     * @Rest\View()
     */
    public function indexAction($path)
    {
        $fileList = [];
        $rootDir = $this->container->getParameter('photos_directory');

        $path = $rootDir . '/' . $path;

        $finder = new Finder();
        $finder
            ->in($path)
            ->depth(0)
        ;

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file;
        }

        return ['files' => iterator_to_array($finder, false)];
    }
}
