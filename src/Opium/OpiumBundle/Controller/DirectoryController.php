<?php

namespace Opium\OpiumBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

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
        $path = $this->getPath($path);
        $fileList = [];

        $files = $this->get('opium.finder.photo')
            ->find($path);

        return [
            'parentDirectory' => $this->getParentPath($path),
            'current' => [
                'path' => $path,
            ],
            'files' => $files,
        ];
    }

    /**
     * photoAction
     *
     * @param mixed $file
     * @param mixed $photo
     * @access public
     * @return Response
     *
     * @Rest\View()
     */
    public function photoAction($path, $photo)
    {
        $file = $this->get('opium.finder.photo')
            ->get($path, $photo);

        return [
            'parentDirectory' => $this->getParentPath($path . '/' . $photo),
            'photo' => $file,
        ];
    }

    /**
     * updateAction
     *
     * @param mixed $path
     * @access public
     * @return void
     *
     * @Rest\View()
     */
    public function updateAction($path, Request $request)
    {
        $path = $this->getPath($path);

        $current = $request->request->get('current');
        if (isset($current['photo'])) {
            $dir = $this->container->getParameter('thumbs_directory') . $path;
            file_put_contents($dir . '/config.yml', 'photo: ' . $current['photo']);
        }

        return $this->indexAction($path);
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

    private function getParentPath($path)
    {
        $parentPath = substr($path, 0, strrpos($path, '/', -2));
        if ($parentPath !== false) {
            if ($parentPath) {
                $parentPath = '/' . $parentPath . '/';
            } else {
                $parentPath = '/';
            }
        }

        return $parentPath;
    }
}
