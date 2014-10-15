<?php

namespace Opium\OpiumBundle\Serializer;

use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Context;
use Symfony\Component\Finder\SplFileInfo;

class SplFileInfoSerializer
{
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
        $info = [
            'path' => $file->getRelativePathname(),
            'type' => $file->isFile() ? 'file' : 'directory',
        ];
        if ($file->isFile()) {
            $imageSize = getimagesize($file->getRealPath());
            $info['mime'] = $imageSize['mime'];
            $info['width'] = $imageSize[0];
            $info['height'] = $imageSize[1];
        }

        return $info;
    }
}
