<?php

namespace App\Entity;

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
     * previous
     *
     * @var File
     * @access private
     */
    private $previous;

    /**
     * next
     *
     * @var File
     * @access private
     */
    private $next;

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
     * getPrevious
     *
     * @access public
     * @return File
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * setPrevious
     *
     * @param File $previous
     * @access public
     * @return File
     */
    public function setPrevious(File $previous = null)
    {
        $this->previous = $previous;
        return $this;
    }

    /**
     * getNext
     *
     * @access public
     * @return File
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * setNext
     *
     * @param File $next
     * @access public
     * @return File
     */
    public function setNext(File $next = null)
    {
        $this->next = $next;
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

    public function prePersist()
    {
        if (!$this->pathname) {
            $parentPathname = $this->getParent() ? $this->getParent()->getPathname() : '';
            if ($parentPathname) {
                $parentPathname .= '/';
            }
            $this->setPathname($parentPathname . $this->getName());
        }
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
