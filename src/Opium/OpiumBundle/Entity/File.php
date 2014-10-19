<?php

namespace Opium\OpiumBundle\Entity;

use JMS\Serializer\Annotation as Serializer;

abstract class File
{
    /**
     * name
     *
     * @var string
     * @access protected
     */
    protected $name;

    /**
     * pathname
     *
     * @var string
     * @access protected
     */
    protected $pathname;

    /**
     * thumbnails
     *
     * @var array
     * @access protected
     */
    protected $thumbnails;

    /**
     * Gets the value of name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name
     *
     * @param string $name file name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the value of pathname
     *
     * @return string
     */
    public function getPathname()
    {
        return $this->pathname;
    }

    /**
     * Sets the value of pathname
     *
     * @param string $pathname path
     *
     * @return File
     */
    public function setPathname($pathname)
    {
        $this->pathname = $pathname;
        return $this;
    }

    /**
     * Gets the value of thumbnails
     *
     * @return array
     */
    public function getThumbnails()
    {
        return $this->thumbnails;
    }

    /**
     * Sets the value of thumbnails
     *
     * @param array thumbnails
     *
     * @return File
     */
    public function setThumbnails(array $thumbnails)
    {
        $this->thumbnails = $thumbnails;
        return $this;
    }

    /**
     * getType
     *
     * @abstract
     * @access public
     * @return string
     *
     * @Serializer\VirtualProperty
     */
    abstract public function getType();
}
