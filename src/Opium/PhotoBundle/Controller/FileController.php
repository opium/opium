<?php

namespace Opium\PhotoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Opium\OpiumBundle\Entity\Photo;
use Opium\PhotoBundle\Http\ImagickResponse;

class FileController extends Controller
{
    /**
     * baseFileAction
     *
     * @param string $path
     * @access public
     * @return BinaryFileResponse
     *
     * @ParamConverter("photo", class="OpiumBundle:Photo", options={"slug" = "slug"})
     */
    public function baseFileAction(Photo $photo)
    {
        $fullPath = $this->container->getParameter('photos_directory') . $photo->getPathname();

        return new BinaryFileResponse($fullPath);
    }

    /**
     * cropImageAction
     *
     * @param string $path
     * @param string $extension
     * @param int $width
     * @param int $height
     * @access public
     * @return Response
     *
     * @ParamConverter("photo", class="OpiumBundle:Photo", options={"slug" = "slug"})
     */
    public function cropImageAction(Photo $photo, $cropWidth, $cropHeight)
    {
        $path = $photo->getPathname();

        $writePath = $this->getWritePath($photo, $cropWidth, $cropHeight);
        if (file_exists($writePath)) {
            return new BinaryFileResponse($writePath);
        }
        $filepath = $this->container->getParameter('photos_directory') . $path;
        $imagick = new \Imagick($filepath);
        if (!in_array($imagick->getImageMimeType(), $this->container->getParameter('allowed_mime_types'))) {
            throw $this->createNotFoundException('Wrong file mime type');
        }

        $crop = new \stojg\crop\CropEntropy();
        $crop->setImage($imagick);
        $imagick = $crop->resizeAndCrop($cropWidth, $cropHeight);
        $imagick->writeImage($writePath);

        return new ImagickResponse($imagick, getimagesize($filepath)['mime']);
    }

    /**
     * getWritePath
     *
     * @param Photo $photo
     * @param int $width
     * @param int $height
     * @access private
     * @return string
     */
    private function getWritePath(Photo $photo, $width, $height)
    {
        $dir = $this->container->getParameter('thumbs_directory') .
            $photo->getPathname() . '/';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir .
            $width . 'x' . $height .
            '.' . $photo->getExtension();
    }
}
