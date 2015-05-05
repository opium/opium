<?php

namespace Opium\OpiumBundle\Transformer;

use Imagick;
use ImagickException;
use Symfony\Component\Finder\SplFileInfo;

use Opium\OpiumBundle\Entity\Directory;
use Opium\OpiumBundle\Entity\File;
use Opium\OpiumBundle\Entity\Photo;

class FileTransformer
{
    /**
     * transform
     *
     * @param SplFileInfo $file
     * @access private
     * @return Directory
     */
    public function transformToDirectory(SplFileInfo $file)
    {
        return $this->transform(new Directory(), $file);
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
        $photo = $this->transform(new Photo(), $file);
        try {
            $imagick = new Imagick($file->getRealPath());
            $photo->setExifData($imagick->getImageProperties('exif:*'));
        } catch (ImagickException $e) {
            ld($e, $file);
        }

        return $photo;
    }

    /**
     * transform
     *
     * @param File $entity
     * @param SplFileInfo $file
     * @access private
     * @return File
     */
    private function transform(File $entity, SplFileInfo $file)
    {
        return $entity->setPathname($file->getRelativePathname())
            ->setName($file->getFilename())
        ;
    }
}
