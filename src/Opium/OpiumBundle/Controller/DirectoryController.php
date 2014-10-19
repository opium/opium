<?php

namespace Opium\OpiumBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

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

        $files = $this->get('opium.finder.photo')
            ->find($path);

        $parentPath = substr($path, 0, strrpos($path, '/', -2));
        if ($parentPath !== false) {
            if ($parentPath) {
                $parentPath = '/' . $parentPath . '/';
            } else {
                $parentPath = '/';
            }
        }

        return [
            'parentDirectory' => $parentPath,
            'files' => $files,
        ];
    }
}
