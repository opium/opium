<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * File
 *
 * @abstract
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
abstract class File
{
    /**
     * name
     *
     * @var string
     * @Groups({"directory_read"})
     */
    protected $name;

    /**
     * pathname
     *
     * @var string
     * @Groups({"directory_read"})
     */
    protected $pathname;

    /**
     * thumbnails
     *
     * @var array
     */
    protected $thumbnails;

    /**
     * id
     *
     * @var string
     */
    private $id;

    /**
     * parent
     *
     * @var Directory
     * @Groups({"directory_read"})
     * @MaxDepth(1)
     */
    private $parent;

    /**
     * previous
     *
     * @var File
     * @Groups({"directory_read"})
     * @MaxDepth(1)
     */
    private $previous;

    /**
     * next
     *
     * @var File
     * @Groups({"directory_read"})
     * @MaxDepth(1)
     */
    private $next;

    /**
     * slug
     *
     * @var string
     * @Groups({"directory_read"})
     */
    private $slug;

    /**
     * getId
     *
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
     *
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
     *
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
     *
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
     *
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
     *
     * @return string
     */
    abstract public function getType();
}
