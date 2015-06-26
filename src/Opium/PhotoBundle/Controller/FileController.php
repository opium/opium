<?php

namespace Opium\PhotoBundle\Controller;

use Imagick;
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

        if ($cropHeight === 'auto') {
            $cropHeight = (int) round($cropWidth * $photo->getHeight() / $photo->getWidth());
        }


        $writePath = $this->getWritePath($photo, $cropWidth, $cropHeight);
        if (file_exists($writePath)) {
            //return new BinaryFileResponse($writePath);
        }
        $filepath = $this->container->getParameter('photos_directory') . $path;
        $imagick = new Imagick($filepath);
        if (!in_array($imagick->getImageMimeType(), $this->container->getParameter('allowed_mime_types'))) {
            throw $this->createNotFoundException('Wrong file mime type');
        }

        $imagick = $this->autoRotateImage($imagick);

        $crop = new \stojg\crop\CropEntropy();
        $crop->setImage($imagick);
        $imagick = $crop->resizeAndCrop($cropWidth, $cropHeight);
        $imagick->setInterlaceScheme(Imagick::INTERLACE_PLANE);
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

    /**
     * autoRotateImage
     *
     * @param mixed $image
     * @access private
     * @return Imagick
     */
    private function autoRotateImage($image)
    {
        $orientation = $image->getImageOrientation();

        switch($orientation) {
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage('#000', 180); // rotate 180 degrees
                break;

            case Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage('#000', 90); // rotate 90 degrees CW
                break;

            case Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage('#000', -90); // rotate 90 degrees CCW
                break;

            default:
                break;
        }

        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);

        return $image;
    }
}
