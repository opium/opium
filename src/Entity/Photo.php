<?php

declare(strict_types=1);

namespace App\Entity;

use App\Component\Layout\RectangleInterface;

/**
 * Photo
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Photo extends File implements RectangleInterface
{
    /**
     * exif
     *
     * @var array
     */
    private $exif;

    /**
     * width
     *
     * @var int
     */
    private $width;

    /**
     * height
     *
     * @var int
     */
    private $height;

    /**
     * latitude
     *
     * @var float
     */
    private $latitude;

    /**
     * longitude
     *
     * @var float
     */
    private $longitude;

    /**
     * displayable
     *
     * @var bool
     */
    private $displayable = false;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'file';
    }

    /**
     * Getter for displayable
     *
     * return boolean
     */
    public function getDisplayable()
    {
        return $this->displayable;
    }

    /**
     * Setter for displayable
     *
     * @param bool $displayable
     *
     * @return Photo
     */
    public function setDisplayable($displayable)
    {
        $this->displayable = $displayable;

        return $this;
    }

    /**
     * getExtension
     *
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->getName(), PATHINFO_EXTENSION);
    }

    /**
     * getPosition
     *
     * @return array
     */
    public function getPosition()
    {
        if (null !== $this->latitude && null !== $this->longitude) {
            return [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ];
        }

        return $this->positionFromExif($this->exif);
    }

    /**
     * Getter for latitude
     *
     * return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Setter for latitude
     *
     * @param float $latitude
     *
     * @return Photo
     */
    public function setLatitude($latitude)
    {
        $this->latitude = (float) $latitude;

        return $this;
    }

    /**
     * Getter for longitude
     *
     * return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Setter for longitude
     *
     * @param float $longitude
     *
     * @return Photo
     */
    public function setLongitude($longitude)
    {
        $this->longitude = (float) $longitude;

        return $this;
    }

    /**
     * getExif
     *
     * @return array
     */
    public function getExif()
    {
        return $this->exif;
    }

    /**
     * setExif
     *
     * @param float $exif
     *
     * @return Photo
     */
    public function setExif($exif)
    {
        $this->exif = $exif;

        return $this;
    }

    /**
     * Getter for width
     *
     * return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Setter for width
     *
     * @param int $width
     *
     * @return Photo
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Getter for height
     *
     * return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Setter for height
     *
     * @param int $height
     *
     * @return Photo
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * positionFromExif
     *
     * @return array
     */
    private function positionFromExif($exif)
    {
        if (empty($exif['GPSLatitude']) || empty($exif['GPSLatitudeRef'])
            || empty($exif['GPSLongitude']) || empty($exif['GPSLongitudeRef'])) {
            return;
        }

        $lat = explode(',', $exif['GPSLatitude']);
        $latRef = $exif['GPSLatitudeRef'];
        $lng = explode(',', $exif['GPSLongitude']);
        $lngRef = $exif['GPSLongitudeRef'];

        return [
            'lat' => $this->toDecimal(trim($lat[0]), trim($lat[1]), trim($lat[2]), $latRef),
            'lng' => $this->toDecimal(trim($lng[0]), trim($lng[1]), trim($lng[2]), $lngRef),
        ];
    }

    /**
     * toDecimal
     *
     * @param mixed $deg
     * @param mixed $min
     * @param mixed $sec
     * @param mixed $hem
     *
     * @return float
     */
    private function toDecimal($deg, $min, $sec, $hem)
    {
        $d = $this->getValue($deg) + $this->getValue($min, 60) + $this->getValue($sec, 36000000);

        return ('S' == $hem || 'W' == $hem) ? $d *= -1 : $d;
    }

    /**
     * getValue
     *
     * @param mixed $deg
     * @param int $multiplier
     *
     * @return float
     */
    private function getValue($deg, $multiplier = 1)
    {
        if (false !== strpos($deg, '/')) {
            [$a, $b] = explode('/', $deg);
            $deg = $a / $b;
        }

        return $deg / $multiplier;
    }
}
