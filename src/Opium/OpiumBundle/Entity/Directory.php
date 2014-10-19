<?php

namespace Opium\OpiumBundle\Entity;

class Directory extends File
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'directory';
    }
}
