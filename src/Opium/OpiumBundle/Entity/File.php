<?php

namespace Opium\OpiumBundle\Entity;

/**
 * File
 *
 * @abstract
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
abstract class File
{
    /**
     * id
     *
     * @var string
     * @access private
     */
    private $id;

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
     * parent
     *
     * @var Directory
     * @access private
     */
    private $parent;

    /**
     * slug
     *
     * @var string
     * @access private
     */
    private $slug;

    /**
     * getId
     *
     * @access public
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * setId
     *
     * @param mixed $id
     * @access private
     * @return File
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

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
     * getParent
     *
     * @access public
     * @return Directory
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(File $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * setSlug
     *
     * @param string $slug
     * @access public
     * @return File
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * getSlug
     *
     * @access public
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * getType
     *
     * @abstract
     * @access public
     * @return string
     */
    abstract public function getType();
}
