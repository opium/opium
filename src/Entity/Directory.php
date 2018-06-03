<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Directory
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Directory extends File
{
    /**
     * children
     *
     * @var mixed
     */
    private $children;

    /**
     * directoryThumbnail
     *
     * @var Photo
     */
    private $directoryThumbnail;

    /**
     * __construct
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
     *
     * @Groups({"directory_read"})
     */
    public function getType()
    {
        return 'directory';
    }

    /**
     * getChildren
     *
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

    /**
     * @Groups({"directory_read"})
     * @MaxDepth(1)
     * TODO MaxDepth(2) in the old version
     */
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
