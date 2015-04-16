<?php

namespace Opium\OpiumBundle\Transformer;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\RouterInterface;

use Opium\OpiumBundle\Entity\Directory;
use Opium\OpiumBundle\Entity\Photo;
use Opium\OpiumBundle\Finder\PhotoFinder;

class FileTransformer
{
    /**
     * photoDir
     *
     * @var string
     * @access private
     */
    private $photoDir;

    /**
     * thumbsDir
     *
     * @var string
     * @access private
     */
    private $thumbsDir;

    /**
     * allowedMimeTypes
     *
     * @var array
     * @access private
     */
    private $allowedMimeTypes;

    /**
     * router
     *
     * @var RouterInterface
     * @access private
     */
    private $router;

    /**
     * finder
     *
     * @var PhotoFinder
     * @access private
     */
    private $finder;

    /**
     * __construct
     *
     * @param string $photoDir
     * @access public
     */
    public function __construct($photoDir, RouterInterface $router, array $allowedMimeTypes, $thumbsDir)
    {
        $this->photoDir = $photoDir;
        $this->router = $router;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->thumbsDir = $thumbsDir;
    }

    public function setFinder(PhotoFinder $finder)
    {
        $this->finder = $finder;
        return $this;
    }

    /**
     * transformToDirectory
     *
     * @param SplFileInfo $file
     * @access private
     * @return Directory
     */
    public function transformToDirectory(SplFileInfo $file)
    {
        $dir = new Directory();

        $path = substr($file->getPathname(), strlen($this->photoDir)) . '/';
        $dir->setPathname($path)
            ->setName($file->getRelativePathname())
        ;


        return $dir;
    }

    /**
     * transformToFile
     *
     * @param SplFileInfo $file
     * @access private
     * @return Photo
     */
    public function transformToFile(SplFileInfo $file)
    {
        $photo = new Photo();
        $path = substr($file->getPathname(), strlen($this->photoDir));
        $photo->setPathname($path)
            ->setName($file->getRelativePathname())
        ;
        return $photo;
    }
}
