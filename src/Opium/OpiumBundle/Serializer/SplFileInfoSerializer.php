<?php

namespace Opium\OpiumBundle\Serializer;

use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Context;
use Symfony\Component\Finder\SplFileInfo;

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
     * __construct
     *
     * @param string $photoDirectory
     * @access public
     * @return void
     */
    public function __construct($photoDirectory, $prefix = '')
    {
        $this->photoDirectoryLength = strlen($photoDirectory);
        $this->prefix = $prefix;
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
        $publicPath = $this->prefix . substr($file->getPathname(), $this->photoDirectoryLength);
        $info = [
            'name' => $file->getRelativePathname(),
            'path' => $publicPath,
            'type' => $file->isFile() ? 'file' : 'directory',
        ];
        if ($file->isFile()) {
            $imageSize = @getimagesize($file->getRealPath());
            if ($imageSize) {
                $info['mime'] = $imageSize['mime'];
                $info['width'] = $imageSize[0];
                $info['height'] = $imageSize[1];
                $info['thumbnails'] = $this->getThumbnails($publicPath);
            }
        }

        return $info;
    }

    private function getThumbnails($path)
    {
        $pathinfo = pathinfo($path);

        return [
            'square-200x200' => $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '/thumbs/200-200.' . $pathinfo['extension']
        ];
    }
}
