<?php

namespace Opium\Component\Layout\Tests\Units;

use atoum;
use Opium\Component\Layout\Rectangle;
use SplFixedArray;

/**
 * Class LineLayout
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class LineLayout extends atoum
{
    /**
     * testComputeHeightOneLineSingle
     *
     * @access public
     * @return void
     */
    public function testComputeHeightOneLineSingle()
    {
        $this
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(1000, 1000),
                ])
            )
            ->then
                ->integer($this->newTestedInstance->computeHeight($rectangleList, 600))
                ->isEqualTo(600)
        ;
    }

    /**
     * testComputeHeightOneLineSimple
     *
     * @access public
     * @return void
     */
    public function testComputeHeightOneLineSimple()
    {
        $this
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(100, 200),
                    new Rectangle(200, 200),
                    new Rectangle(300, 100),
                ])
            )
            ->then
                ->integer($this->newTestedInstance->computeHeight($rectangleList, 450))
                    ->isEqualTo(100)

            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(200, 500),
                    new Rectangle(300, 750),
                    new Rectangle(400, 1000),
                ])
            )
            ->then
                ->integer($this->newTestedInstance->computeHeight($rectangleList, 600))
                    ->isEqualTo(499) // weird comportment, should be 500
        ;
    }

    /**
     * testComputeHeightOneLine
     *
     * @access public
     * @return void
     */
    public function testComputeHeightOneLine()
    {
        $this
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(100, 200),
                    new Rectangle(200, 200),
                    new Rectangle(300, 100),
                ])
            )
            ->then
                ->integer($this->newTestedInstance->computeHeight($rectangleList, 1000))
                    ->isEqualTo(222)

            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(200, 500),
                    new Rectangle(300, 650),
                    new Rectangle(400, 1000),
                ])
            )
            ->then
                ->integer($this->newTestedInstance->computeHeight($rectangleList, 1100))
                    ->isEqualTo(871)
        ;
    }

    /**
     * testComputeHeightMargin
     *
     * @access public
     * @return void
     */
    public function testComputeHeightMargin()
    {
        $this
            // without margin
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(100, 200),
                    new Rectangle(200, 200),
                    new Rectangle(300, 100),
                ])
            )
            ->then
                ->integer($this->newTestedInstance->computeHeight($rectangleList, 600, 0))
                    ->isEqualTo(133)

            // with margin
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(100, 200),
                    new Rectangle(200, 200),
                    new Rectangle(300, 100),
                ])
            )
            ->then
                ->integer($this->newTestedInstance->computeHeight($rectangleList, 600, 100))
                    ->isNotEqualTo(133)
                    ->isEqualTo(66)
        ;
    }

    /**
     * testComputeRectangleList
     *
     * @access public
     * @return void
     */
    public function testComputeRectangleList()
    {
        $this
            // simple case
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(1000, 2000),
                    new Rectangle(2000, 2000),
                    new Rectangle(3000, 1000),
                    new Rectangle(2500, 1000),
                    new Rectangle(2500, 1000),
                ])
            )
            ->then
                ->object($computed = $this->newTestedInstance->computeRectangleList($rectangleList, 600, 200))
                    // ->then(ldd(iterator_to_array($computed)))
                    // ->isInstanceOf('\SplObjectStorage')
                ->integer(count($computed))
                    ->isEqualTo(2)

                ->object($computed[0][0]['geometry'])
                    ->isInstanceOf('\Opium\Component\Layout\RectangleInterface')

                // first line
                ->integer($computed[0][0]['geometry']->getHeight())
                    ->isEqualTo($computed[0][1]['geometry']->getHeight())
                    ->isEqualTo($computed[0][2]['geometry']->getHeight())

                // second line
                ->integer($computed[1][0]['geometry']->getHeight())
                    ->isEqualTo($computed[1][1]['geometry']->getHeight())


            // add last line
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(1000, 2000),
                    new Rectangle(2000, 2000),
                    new Rectangle(3000, 1000),
                    new Rectangle(2500, 1000),
                    new Rectangle(2500, 1000),
                    new Rectangle(1000, 1000),
                ])
            )
            ->then
                ->object($computed = $this->newTestedInstance->computeRectangleList($rectangleList, 600, 200))
                    ->isInstanceOf('\SplQueue')
                ->integer(count($computed))
                    ->isEqualTo(3)

                ->object($computed[0][0]['geometry'])
                    ->isInstanceOf('\Opium\Component\Layout\RectangleInterface')
                ->integer($computed[0][0]['geometry']->getHeight())
                    ->isEqualTo($computed[0][1]['geometry']->getHeight())
                    ->isEqualTo($computed[0][2]['geometry']->getHeight())
                ->integer($computed[0][0]['geometry']->getHeight())
                    ->isEqualTo(133)

                ->integer($computed[1][0]['geometry']->getHeight())
                    ->isEqualTo($computed[1][1]['geometry']->getHeight())
                    ->isEqualTo(120)

                ->integer($computed[2][0]['geometry']->getHeight())
                    ->isEqualTo(200)
        ;
    }

    /**
     * testMargin
     *
     * @access public
     * @return void
     */
    public function testMargin()
    {

        $this
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(1000, 2000),
                    new Rectangle(2000, 2000),
                    new Rectangle(3000, 1000),
                    new Rectangle(2500, 1000),
                    new Rectangle(2500, 1000),
                    new Rectangle(1000, 1000),
                ])
            )
            ->then
                ->object($computed = $this->newTestedInstance->computeRectangleList($rectangleList, 600, 200, 5))
                    ->isInstanceOf('\SplQueue')
                ->integer(count($computed))
                    ->isEqualTo(3)

                ->object($computed[0][0]['geometry'])
                    ->isInstanceOf('\Opium\Component\Layout\RectangleInterface')
                ->integer($computed[0][0]['geometry']->getHeight())
                    ->isEqualTo($computed[0][1]['geometry']->getHeight())
                    ->isEqualTo($computed[0][2]['geometry']->getHeight())
                ->integer($computed[0][0]['geometry']->getHeight())
                    ->isEqualTo(130)

                ->integer($computed[1][0]['geometry']->getHeight())
                    ->isEqualTo($computed[1][1]['geometry']->getHeight())

                ->integer($computed[1][0]['geometry']->getHeight())
                    ->isEqualTo(118)

                ->integer($computed[2][0]['geometry']->getHeight())
                    ->isEqualTo(200)

            // real test cases
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(5923, 3729),
                    new Rectangle(3072, 2304),
                    new Rectangle(2593, 3872),
                ])
            )
            ->then
                ->object($computed = $this->newTestedInstance->computeRectangleList($rectangleList, 600, 200, 10))
                    ->isInstanceOf('\SplQueue')
                ->integer(count($computed))
                    ->isEqualTo(2)
                ->integer($computed[0][0]['geometry']->getHeight())
                    ->isEqualTo(198)

                ->if(
                        $totalWidth = $computed[0][0]['geometry']->getWidth()
                            + $computed[0][1]['geometry']->getWidth()
                )
                ->float($totalWidth)
                    ->isNearlyEqualTo(578.49557522124)
        ;
    }
}
