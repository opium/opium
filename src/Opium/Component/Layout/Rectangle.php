<?php

namespace Opium\Component\Layout;

use JsonSerializable;

/**
 * Class Rectangle
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Rectangle implements RectangleInterface, JsonSerializable
{
    /**
     * width
     *
     * @var int
     * @access private
     */
    private $width;

    /**
     * height
     *
     * @var int
     * @access private
     */
    private $height;

    /**
     * __construct
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
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
     * @return Rectangle
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
     * @return Rectangle
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ];
    }
}
