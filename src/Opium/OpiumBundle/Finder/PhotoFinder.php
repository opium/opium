<?php

namespace Opium\OpiumBundle\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Opium\OpiumBundle\Transformer\FileTransformer;

class PhotoFinder
{
    /**
     * photoDir
     *
     * @var string
     * @access private
     */
    private $photoDir;

    /**
     * fileTransformer
     *
     * @var FileTransformer
     * @access private
     */
    private $fileTransformer;

    /**
     * __construct
     *
     * @param string $photoDir
     * @param FileTransformer $fileTransformer
     * @access public
     */
    public function __construct($photoDir, FileTransformer $fileTransformer)
    {
        $this->photoDir = $photoDir;
        $this->fileTransformer = $fileTransformer;
    }

    /**
     * find
     *
     * @param string $path
     * @access public
     * @return array
     */
    public function find($path)
    {
        $absolutePath = $this->photoDir . $path;

        $finder = new Finder();
        $finder
            ->in($absolutePath)
            ->depth(0)
            ->sortByType()
        ;

        $files = [];
        foreach ($finder as $file) {
            if ($file->isDir()) {
                $files[] = $this->fileTransformer->transformToDirectory($file);
            } elseif ($file->isFile()) {
                $files[] = $this->fileTransformer->transformToFile($file);
            }
        }

        return $files;
    }

    /**
     * get
     *
     * @param string $path
     * @access public
     * @return void
     */
    public function get($path)
    {
        $relativePathname = $path;
        $absolutePath = $this->photoDir . $relativePathname;

        $file = new SplFileInfo($absolutePath, $path, $relativePathname);

        if ($file->isDir()) {
            return $this->fileTransformer->transformToDirectory($file);
        } elseif ($file->isFile()) {
            return $this->fileTransformer->transformToFile($file);
        }
    }
}
