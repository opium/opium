<?php

namespace Opium\OpiumBundle\Entity;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Photo
 *
 * @uses File
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Photo extends File
{
    /**
     * exif
     *
     * @var array
     * @access private
     */
    private $exif;

    /**
     * displayable
     *
     * @var boolean
     * @access private
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
     * @param boolean $displayable
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
     * @access public
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->getName(), PATHINFO_EXTENSION);
    }

    /**
     * getPosition
     *
     * @access public
     * @return array
     */
    public function getPosition()
    {
        return $this->positionFromExif($this->exif);
    }

    /**
     * getExif
     *
     * @access public
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
     * @access public
     * @return Photo
     */
    public function setExif($exif)
    {
        $this->exif = $exif;
        return $this;
    }

    /**
     * positionFromExif
     *
     * @access private
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
     * @access private
     * @return double
     */
    private function toDecimal($deg, $min, $sec, $hem)
    {
        $d = $this->getValue($deg) + $this->getValue($min, 60) + $this->getValue($sec, 36000000);
        return ($hem=='S' || $hem=='W') ? $d *= -1 : $d;
    }

    /**
     * getValue
     *
     * @param mixed $deg
     * @param int $multiplier
     * @access private
     * @return double
     */
    private function getValue($deg, $multiplier = 1)
    {
        if (strpos($deg, '/') !== false) {
            list($a, $b) = explode('/', $deg);
            $deg = $a / $b;
        }

        return $deg / $multiplier;
    }
}
