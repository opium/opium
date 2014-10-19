<?php

namespace Opium\OpiumBundle\Entity;

class Photo extends File
{
    /**
     * image
     *
     * @var mixed
     * @access private
     */
    private $image;

    /**
     * Gets the value of image
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image
     *
     * @param mixed $image image
     *
     * @return Photo
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'file';
    }
}
