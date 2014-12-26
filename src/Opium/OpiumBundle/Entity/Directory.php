<?php

namespace Opium\OpiumBundle\Entity;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Directory
 *
 * @uses File
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Directory extends File
{
    private $children;

    /**
     * directoryThumbnail
     *
     * @var Photo
     * @access private
     */
    private $directoryThumbnail;

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
        if (!isset($this->children)) {
            $this->children = $this->finder->find($this->getPathname());
        }

        return $this->children;
    }
}
