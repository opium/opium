<?php

declare(strict_types=1);

namespace App\Component\Layout;

/**
 * Interface RectangleInterface
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
interface RectangleInterface
{
    /**
     * getWidth
     *
     * @return int
     */
    public function getWidth();

    /**
     * getHeight
     *
     * @return int
     */
    public function getHeight();
}
