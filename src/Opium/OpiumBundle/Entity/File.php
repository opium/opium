<?php

namespace Opium\OpiumBundle\Entity;

use Hateoas\Configuration\Annotation as Hateoas;

use Opium\OpiumBundle\Finder\PhotoFinder;

/**
 * File
 *
 * @abstract
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
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
     * finder
     *
     * @var PhotoFinder
     * @access protected
     */
    protected $finder;

    /**
     * parent
     *
     * @var Directory
     * @access private
     */
    private $parent;

    /**
     * setFinder
     *
     * @param PhotoFinder $finder
     * @access public
     * @return File
     */
    public function setFinder(PhotoFinder $finder)
    {
        $this->finder = $finder;
        return $this;
    }

    /**
     * getId
     *
     * @access public
     * @return string
     */
    public function getId()
    {
        $id = urlencode($this->getPathname());
        return $id ? $id : null;
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
     * getParentPath
     *
     * @access public
     * @return string
     */
    public function getParentPath()
    {
        $path = $this->getPathname();
        if (strlen($path) < 2) {
            return '';
        }

        $parentPath = substr($path, 0, strrpos($path, '/', -2));

        return $parentPath;
    }

    /**
     * getParent
     *
     * @access public
     * @return Directory
     */
    public function getParent()
    {
        if ($this->getPathname() == '/') {
            return null;
        }

        if (!isset($this->parent)) {
            $this->parent = $this->finder->get($this->getParentPath());
        }
        return $this->parent;
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
