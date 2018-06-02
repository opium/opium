<?php

declare(strict_types=1);

namespace App\Transformer;

use App\Entity\Directory;
use App\Entity\File;
use App\Entity\Photo;
use Imagick;
use ImagickException;
use Symfony\Component\Finder\SplFileInfo;

class FileTransformer
{
    /**
     * allowedMimeTypes
     *
     * @var array
     */
    private $allowedMimeTypes;

    /**
     * __construct
     *
     * @param array $allowedMimeTypes
     */
    public function __construct(array $allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * transform
     *
     * @param SplFileInfo $file
     *
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
     *
     * @return Photo
     */
    public function transformToFile(SplFileInfo $file)
    {
        $photo = $this->transform(new Photo(), $file);
        try {
            $imagick = new Imagick($file->getRealPath());

            if (in_array($imagick->getImageMimeType(), $this->allowedMimeTypes)) {
                $photo->setDisplayable(true);
            }

            // treat width & height
            $geometry = $imagick->getImageGeometry();

            if (in_array(
                $imagick->getImageOrientation(),
                [Imagick::ORIENTATION_RIGHTTOP, Imagick::ORIENTATION_LEFTBOTTOM]
            )) {
                $photo->setWidth($geometry['height'])
                    ->setHeight($geometry['width']);
            } else {
                $photo->setWidth($geometry['width'])
                    ->setHeight($geometry['height']);
            }

            // treat Exif datas
            $tmpExif = $imagick->getImageProperties('exif:*');
            $exif = [];
            foreach ($tmpExif as $key => $value) {
                $exif[str_replace('exif:', '', $key)] = $value;
            }

            if (!empty($exif)) {
                $photo->setExif($exif);
            }
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
     *
     * @return File
     */
    private function transform(File $entity, SplFileInfo $file)
    {
        return $entity->setPathname($file->getRelativePathname())
            ->setName($file->getFilename())
        ;
    }
}
