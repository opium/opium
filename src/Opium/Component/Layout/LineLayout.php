<?php

namespace Opium\Component\Layout;

use Opium\Component\Layout\RectangleInterface;
use SplObjectStorage;
use SplQueue;
use Traversable;

/**
 * Class LineLayout
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class LineLayout
{
    /**
     * computeHeight
     *
     * @param Traversable $rectangleList
     * @param int $maxWidth
     * @param int $gutterWidth
     * @access public
     * @return int
     */
    public function computeHeight(Traversable $rectangleList, $maxWidth, $gutterWidth = 0)
    {
        $ratioSum = $this->reduceIterator(
            $rectangleList,
            function ($carry, $item) {
                if ($item instanceof RectangleInterface && $item->getHeight()) {
                    $carry += $item->getWidth()  / $item->getHeight();
                } else {
                    $carry += 1;
                }

                return $carry;
            }
        );

        $gutterTotal = $gutterWidth * count($rectangleList);

        $height = ($maxWidth - $gutterTotal) / $ratioSum;

        // todo replace by intdiv in php 7
        return intval($height);
    }

    /**
     * computeRectangleList
     *
     * @param Traversable $rectangleList
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $gutterWidth
     * @access public
     * @return SplQueue<SplQueue<Rectangle>>
     */
    public function computeRectangleList(Traversable $rectangleList, $maxWidth, $maxHeight, $gutterWidth = 0)
    {
        $storage = new SplQueue();

        $line = new SplQueue();
        foreach ($rectangleList as $rectangle) {
            $line->enqueue($rectangle);
            $height = $this->computeHeight($line, $maxWidth, $gutterWidth);

            if ($height <= $maxHeight) {
                $storage = $this->computeLine($storage, $line, $height);

                $line = new SplQueue();

            }
        }

        if (count($line) > 0) {
            $storage = $this->computeLine($storage, $line, $maxHeight);
        }

        return $storage;
    }

    /**
     * computeLine
     *
     * @param SplQueue $storage
     * @param SplQueue $line
     * @param int $height
     * @access private
     * @return SplQueue
     */
    private function computeLine(SplQueue $storage, SplQueue $line, $height)
    {
        $outLine = new SplQueue();
        foreach ($line as $item) {
            if ($item instanceof RectangleInterface && $item->getHeight()) {
                $width = $item->getWidth() * $height / $item->getHeight();
            } else {
                $width = $height;
            }
            $geometry = new Rectangle($width, $height);
            $outLine->enqueue(['item' => $item, 'geometry' => $geometry]);
        }
        $storage->enqueue($outLine);

        return $storage;
    }

    /**
     * reduceIterator
     *
     * @param Traversable $iterator
     * @param callable $callback
     * @access private
     * @return mixed
     */
    private function reduceIterator(Traversable $iterator, callable $callback)
    {
        $carry = 0;
        foreach ($iterator as $item)
        {
            $carry = $callback($carry, $item);
        }

        return $carry;
    }
}
