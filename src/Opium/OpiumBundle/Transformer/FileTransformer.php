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
        $dir->setFinder($this->finder);

        $path = substr($file->getPathname(), strlen($this->photoDir)) . '/';
        $dir->setPathname($path)
            ->setName($file->getRelativePathname());

        $thumbnailFile = $this->getDirectoryThumbnail($file, $dir);

        if ($thumbnailFile) {
            $dir->setThumbnails($this->transformToFile($thumbnailFile)->getThumbnails());
        }

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
        $photo->setFinder($this->finder);
        $path = substr($file->getPathname(), strlen($this->photoDir));
        $photo->setPathname($path)
            ->setName($file->getRelativePathname());

        $imageSize = @getimagesize($file->getRealPath());
        if ($imageSize && in_array($imageSize['mime'], $this->allowedMimeTypes)) {
            $imgPath = substr($file->getPathname(), strlen($this->photoDir));
            $image = [
                'mime' => $imageSize['mime'],
                'width' => $imageSize[0],
                'height' => $imageSize[1],
                'original' => $this->router->generate('basefile', ['path' => $imgPath]),
            ];

            $photo->setImage($image)
                ->setThumbnails($this->getThumbnails($imgPath));
        }

        return $photo;
    }

    /**
     * getThumbnails
     *
     * @param mixed $path
     * @access private
     * @return array
     */
    private function getThumbnails($path)
    {
        if (substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }

        $pathinfo = pathinfo($path);
        $sizes = [
            'square-200x200' => ['w' => 200, 'h' => 200],
            'banner-1170x400' => ['w' => 1170, 'h' => 400],
        ];

        $thumbs = [];
        foreach ($sizes as $name => $size) {
            $thumbs[$name] = $this->router
                ->generate(
                    'image_crop',
                    [
                        'path' => $pathinfo['dirname'] . '/' . $pathinfo['filename'],
                        'width' => $size['w'],
                        'height' => $size['h'],
                        'extension' => $pathinfo['extension'],
                    ]
                );

        }

        return $thumbs;
    }

    /**
     * getDirectoryThumbnail
     *
     * @param Directory $dir
     * @param SplFileInfo $file
     * @access private
     * @return string
     */
    private function getDirectoryThumbnail(SplFileInfo $file, Directory $dir)
    {

        $finder = new \Symfony\Component\Finder\Finder();
        $files = $finder->files()
            ->in($file->getPathname() . '/')
            ->depth(0)
            ->filter(function (\SplFileInfo $tmpFile) {
                if (!in_array(strtolower($tmpFile->getExtension()), ['png', 'jpg', 'jpeg'])) {
                    return false;
                }
                $photo = $this->transformToFile($tmpFile);
                if (!$photo->getImage()) {
                    return false;
                }
            })
            //->sortByName()
            ;

        $configFile = $this->thumbsDir . $dir->getName() . '/config.yml';
        if (file_exists($configFile)) {
            $yaml = new \Symfony\Component\Yaml\Parser;
            $config = $yaml->parse(file_get_contents($configFile));
            $files->name($config['photo']);
        }

        // get first
        $thumbnailFile = null;
        foreach ($files as $thumbnailFile) {
            return $thumbnailFile;
        }
    }
}
