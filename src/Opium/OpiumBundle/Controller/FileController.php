<?php

namespace Opium\OpiumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Opium\OpiumBundle\Http\ImagickResponse;

class FileController extends Controller
{
    public function baseFileAction($path)
    {
        $fullPath = $this->container->getParameter('photos_directory') . $path;
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
     */
    public function cropImageAction($path, $extension, $width, $height)
    {
        $writePath = $this->getWritePath($path, $extension, $width, $height);
        if (file_exists($writePath)) {
            return new BinaryFileResponse($writePath);
        }
        $filepath = $this->container->getParameter('photos_directory') . $path . '.' . $extension;
        $imagick = new \Imagick($filepath);
        if (!in_array($imagick->getImageMimeType(), $this->container->getParameter('allowed_mime_types'))) {
            throw $this->createNotFoundException('Wrong file mime type');
        }

        $crop = new \stojg\crop\CropEntropy();
        $crop->setImage($imagick);
        $imagick = $crop->resizeAndCrop($width, $height);
        $imagick->writeImage($writePath);

        return new ImagickResponse($imagick, getimagesize($filepath)['mime']);
    }

    /**
     * getWritePath
     *
     * @param string $path
     * @param string $extension
     * @param int $width
     * @param int $height
     * @access private
     * @return string
     */
    private function getWritePath($path, $extension, $width, $height)
    {
        $dir = $this->container->getParameter('thumbs_directory') .
            $path . '/';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir .
            $width . 'x' . $height .
            '.' . $extension;
    }
}
