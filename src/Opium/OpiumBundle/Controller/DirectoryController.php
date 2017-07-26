<?php

namespace Opium\OpiumBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Opium\OpiumBundle\Entity\Directory;
use Symfony\Component\Finder\SplFileInfo;
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
     * uploadRootDirectoryAction
     *
     * @access public
     * @return void
     *
     * @ApiDoc(description="Upload a file to a directory")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Post("directories/upload")
     */
    public function uploadRootDirectoryAction(Request $request)
    {
        return $this->uploadDirectoryAction('', $request);
    }

    /**
     * uploadDirectoryAction
     *
     * @param mixed $path
     * @access public
     * @return void
     *
     * @ApiDoc(description="Upload a file to a directory")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\Post("directories/{path}/upload")
     */
    public function uploadDirectoryAction($path, Request $request)
    {
        $dir = $this->get('opium.repository.directory')->findOneBySlug($path);
        $uploadedFile = $request->files->get('file');

        if ($uploadedFile) {
            $originalName = $uploadedFile->getClientOriginalName();
            $photoDir = $this->getParameter('photos_directory');
            $pathname = $dir->getPathname() . '/' . $originalName;
            $tmpFile = $uploadedFile->move($photoDir . $dir->getPathname(), $originalName);

            $file = new SplFileInfo($tmpFile->getRealPath(), $photoDir, $pathname);


            $entity = $this->get('opium.transformer.file')->transformToFile($file);

            $repo = $this->get('opium.repository.file');
            $em = $this->get('doctrine.orm.opium_entity_manager');

            if ($existingFile = $repo->findOneByPathname($entity->getPathname())) {
                $em->remove($existingFile);
            }

            // parent
            $entity->setParent($dir);

            $em->persist($entity);
            $em->flush();

            return $entity;
        }
    }

    /**
     * postDirectoryAction
     *
     * @access public
     * @return void
     *
     * @ApiDoc(description="Create a directory")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function postDirectoryAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $directoryRepository = $em->getRepository('OpiumBundle:Directory');
        $post = $request->request->all();

        $parentId = $post['parent']['id'] ?? null;
        $parentSlug = $post['parent']['slug'] ?? null;
        if ($parentId > 0) {
            $parent = $directoryRepository->find((int) $parentId);
        } elseif ($parentSlug) {
            $parent = $directoryRepository->findOneBySlug($parentSlug);
        } else {
            $parent = $directoryRepository->findOneByParent(null);
        }

        $directory = new Directory();
        $directory->setName($post['name']);
        $directory->setParent($parent);

        $em->persist($directory);
        $em->flush();

        return $directory;
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
