<?php

namespace Opium\Component\Layout;

use Iterator;
use SplQueue;

/**
 * Class LineLayout
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class LineLayout
{
    /**
     * computeHeight
     *
     * @param array $rectangleList
     * @param int $maxWidth
     * @access public
     * @return int
     */
    public function computeHeight(Iterator $rectangleList, $maxWidth)
    {
        $ratioSum = $this->reduceIterator(
            $rectangleList,
            function ($carry, $item) {
                $carry += $item->getWidth() / $item->getHeight();

                return $carry;
            }
        );
        $height = $maxWidth / $ratioSum;

        return $height;
    }

    /**
     * computeRectangleList
     *
     * @param array $rectangleList
     * @param int $maxWidth
     * @param int $maxHeight
     * @access public
     * @return SplObjectStorage<SplQueue<Rectangle>>
     */
    public function computeRectangleList(Iterator $rectangleList, $maxWidth, $maxHeight)
    {
        $lines = new SplQueue();

        $line = new SplQueue();
        foreach ($rectangleList as $rectangle) {
            $line->enqueue($rectangle);
            $height = $this->computeHeight($line, $maxWidth);

            if ($height <= $maxHeight) {
                $lines->enqueue($line);
                $line = new SplQueue();
            }
        }

        if (count($line) > 0) {
            $lines->enqueue($line);
        }

        return $lines;
    }

    /**
     * reduceIterator
     *
     * @param \Iterator $iterator
     * @param callable $callback
     * @access private
     * @return mixed
     */
    private function reduceIterator(\Iterator $iterator, callable $callback)
    {
        $carry = 0;
        foreach ($iterator as $item)
        {
            $carry = $callback($carry, $item);
        }

        return $carry;
    }
}
