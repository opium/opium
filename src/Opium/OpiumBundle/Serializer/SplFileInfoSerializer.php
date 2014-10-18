<?php

namespace Opium\OpiumBundle\Serializer;

use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Context;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SplFileInfoSerializer
{
    /**
     * photoDirectoryLength
     *
     * @var int
     * @access private
     */
    private $photoDirectoryLength;

    /**
     * router
     *
     * @var RouterInterface
     * @access private
     */
    private $router;

    /**
     * requestStack
     *
     * @var RequestStack
     * @access private
     */
    private $requestStack;

    /**
     * allowedMimeTypes
     *
     * @var array
     * @access private
     */
    private $allowedMimeTypes;

    /**
     * __construct
     *
     * @param string $photoDirectory
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     * @param array $allowedMimeTypes
     * @access public
     */
    public function __construct($photoDirectory, RouterInterface $router, RequestStack $requestStack, $allowedMimeTypes)
    {
        $this->photoDirectoryLength = strlen($photoDirectory);
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * serializeSplFileInfoToJson
     *
     * @param VisitorInterface $visitor
     * @param SplFileInfo $file
     * @param array $type
     * @param Context $context
     * @access public
     * @return void
     */
    public function serializeSplFileInfoToJson( VisitorInterface $visitor, SplFileInfo $file, array $type, Context $context)
    {
        $path = substr($file->getPathname(), $this->photoDirectoryLength)
            . ($file->isDir() ? '/' : '');
        $format = $this->requestStack->getMasterRequest()->attributes->get('_format');
        //$apiPath = $this->router
        //    ->generate(
        //        'opium_directory',
        //        [
        //            'path' =>  $path,
        //            '_format' => $format
        //        ]
        //    );

        $info = [
            'name' => $file->getRelativePathname(),
            'pathname' => $path,
            //'api' => [ 'path' => $apiPath ],
            'type' => $file->isFile() ? 'file' : 'directory',
        ];
        if ($file->isFile()) {
            $imageSize = @getimagesize($file->getRealPath());
            if ($imageSize && in_array($imageSize['mime'], $this->allowedMimeTypes)) {
                $imgPath = substr($file->getPathname(), $this->photoDirectoryLength);
                $info['image'] = [
                    'mime' => $imageSize['mime'],
                    'width' => $imageSize[0],
                    'height' => $imageSize[1],
                    'original' => $this->router->generate('basefile', ['path' => $imgPath]),
                    'thumbnails' => $this->getThumbnails($imgPath),
                ];
            }
        }

        return $info;
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
