<?php

namespace Opium\OpiumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Directory
 *
 * @uses File
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Directory extends File
{
    /**
     * children
     *
     * @var mixed
     * @access private
     */
    private $children;

    /**
     * directoryThumbnail
     *
     * @var Photo
     * @access private
     */
    private $directoryThumbnail;

    /**
     * __construct
     *
     * @access public
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Gets the value of directoryThumbnail
     *
     * @return Photo
     */
    public function getDirectoryThumbnail()
    {
        return $this->directoryThumbnail;
    }

    /**
     * Sets the value of directoryThumbnail
     *
     * @param string $directoryThumbnail description
     *
     * @return Directory
     */
    public function setDirectoryThumbnail(Photo $directoryThumbnail)
    {
        $this->directoryThumbnail = $directoryThumbnail;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'directory';
    }

    /**
     * getChildren
     *
     * @access public
     * @return \Iterator
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    public function getDisplayableChildren()
    {
        $out = [];
        foreach ($this->children as $children) {
            if (!$children instanceof Photo || $children->getDisplayable()) {
                $out[] = $children;
            }
        }

        return $out;
    }
}
