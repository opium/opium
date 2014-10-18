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

        $absolutePath = $rootDir . $path;
        $parentPath = substr($path, 0, strrpos($path, '/', -2));
        if ($parentPath !== false) {
            if ($parentPath) {
                $parentPath = '/' . $parentPath . '/';
            } else {
                $parentPath = '/';
            }
        }

        $finder = new Finder();
        $finder
            ->in($absolutePath)
            ->depth(0)
            ->sortByType()
        ;

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file;
        }

        return [
            'parentDirectory' => $parentPath,
            'files' => iterator_to_array($finder, false)
        ];
    }
}
