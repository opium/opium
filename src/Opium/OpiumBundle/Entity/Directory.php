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
