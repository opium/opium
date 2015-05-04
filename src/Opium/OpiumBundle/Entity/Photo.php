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
     * image
     *
     * @var mixed
     * @access private
     */
    private $image;

    private $position;

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

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }
}
