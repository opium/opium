<?php

namespace Opium\OpiumBundle\Transformer;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\RouterInterface;

use Opium\OpiumBundle\Entity\Directory;
use Opium\OpiumBundle\Entity\Photo;

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
     * __construct
     *
     * @param string $photoDir
     * @access public
     */
    public function __construct($photoDir, RouterInterface $router, array $allowedMimeTypes)
    {
        $this->photoDir = $photoDir;
        $this->router = $router;
        $this->allowedMimeTypes = $allowedMimeTypes;
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
            ->setName($file->getRelativePathname());

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

        // get first
        $thumbnailFile = null;
        foreach ($files as $thumbnailFile) {
            break;
        }

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
        $path = substr($file->getPathname(), strlen($this->photoDir)) . '/';
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
        $square200x200 = $this->router
            ->generate(
                'image_crop',
                [
                    'path' => $pathinfo['dirname'] . '/' . $pathinfo['filename'],
                    'width' => 200,
                    'height' => 200,
                    'extension' => $pathinfo['extension'],
                ]
            );

        return [
            'square-200x200' => $square200x200,
        ];
    }
}
