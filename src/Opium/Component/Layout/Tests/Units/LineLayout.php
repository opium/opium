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
                    ->isEqualTo(76)
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
                    ->isInstanceOf('\SplObjectStorage')
                ->integer(count($computed))
                    ->isEqualTo(5)

                ->object($computed[$rectangleList[0]])
                    ->isInstanceOf('\Opium\Component\Layout\RectangleInterface')

                // first line
                ->integer($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[1]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[2]]->getHeight())

                // second line
                ->integer($computed[$rectangleList[3]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[4]]->getHeight())


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
                    ->isInstanceOf('\SplObjectStorage')
                ->integer(count($computed))
                    ->isEqualTo(6)

                ->object($computed[$rectangleList[0]])
                    ->isInstanceOf('\Opium\Component\Layout\RectangleInterface')
                ->integer($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[1]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[2]]->getHeight())
                ->integer($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo(133)

                ->integer($computed[$rectangleList[3]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[4]]->getHeight())
                    ->isEqualTo(120)

                ->integer($computed[$rectangleList[5]]->getHeight())
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
                    ->isInstanceOf('\SplObjectStorage')
                ->integer(count($computed))
                    ->isEqualTo(6)

                ->object($computed[$rectangleList[0]])
                    ->isInstanceOf('\Opium\Component\Layout\RectangleInterface')
                ->integer($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[1]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[2]]->getHeight())
                ->integer($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo(131)

                ->integer($computed[$rectangleList[3]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[4]]->getHeight())

                ->integer($computed[$rectangleList[3]]->getHeight())
                    ->isEqualTo(118)

                ->integer($computed[$rectangleList[5]]->getHeight())
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
                    ->isInstanceOf('\SplObjectStorage')
                ->integer($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo(163)

                ->if(
                        $totalWidth = $computed[$rectangleList[0]]->getWidth()
                            + $computed[$rectangleList[1]]->getWidth()
                            + $computed[$rectangleList[2]]->getWidth()
                )
                ->integer($totalWidth)
                    ->isEqualTo(580)
        ;
    }
}
