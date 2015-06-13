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
                ->float($this->newTestedInstance->computeHeight($rectangleList, 450))
                    ->isNearlyEqualTo(100)

            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(200, 500),
                    new Rectangle(300, 750),
                    new Rectangle(400, 1000),
                ])
            )
            ->then
                ->float($this->newTestedInstance->computeHeight($rectangleList, 600))
                    ->isNearlyEqualTo(500)
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
                ->float(round($this->newTestedInstance->computeHeight($rectangleList, 1000), 2))
                    ->isEqualTo(222.22)

            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(200, 500),
                    new Rectangle(300, 650),
                    new Rectangle(400, 1000),
                ])
            )
            ->then
                ->float(round($this->newTestedInstance->computeHeight($rectangleList, 1100), 2))
                    ->isEqualTo(871.95)
        ;
    }

    /**
     * testComputeHeightMargin
     *
     * @access public
     * @return void
     *
     * @tags tmp
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
                ->float(round($this->newTestedInstance->computeHeight($rectangleList, 600, 0), 2))
                    ->isEqualTo(133.33)

            // with margin
            ->if(
                $rectangleList = SplFixedArray::fromArray([
                    new Rectangle(100, 200),
                    new Rectangle(200, 200),
                    new Rectangle(300, 100),
                ])
            )
            ->then
                ->float(round($this->newTestedInstance->computeHeight($rectangleList, 600, 100), 2))
                    ->isNotEqualTo(133.33)
                    ->isEqualTo(76.92)
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
                ->float($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[1]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[2]]->getHeight())

                // second line
                ->float($computed[$rectangleList[3]]->getHeight())
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
                ->float($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[1]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[2]]->getHeight())
                ->float(round($computed[$rectangleList[0]]->getHeight()))
                    ->isEqualTo(133)

                ->float($computed[$rectangleList[3]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[4]]->getHeight())
                    ->isEqualTo(120)

                ->float(round($computed[$rectangleList[5]]->getHeight()))
                    ->isEqualTo(200)
        ;
    }

    /**
     * testMargin
     *
     * @access public
     * @return void
     *
     * @tags tmp2
     */
    public function testMargin()
    {

        $this
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
                ->object($computed = $this->newTestedInstance->computeRectangleList($rectangleList, 600, 200, 5))
                    ->isInstanceOf('\SplObjectStorage')
                ->integer(count($computed))
                    ->isEqualTo(6)

                ->object($computed[$rectangleList[0]])
                    ->isInstanceOf('\Opium\Component\Layout\RectangleInterface')
                ->float($computed[$rectangleList[0]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[1]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[2]]->getHeight())
                ->float(round($computed[$rectangleList[0]]->getHeight()))
                    ->isEqualTo(132)

                ->float($computed[$rectangleList[3]]->getHeight())
                    ->isEqualTo($computed[$rectangleList[4]]->getHeight())

                ->float(round($computed[$rectangleList[3]]->getHeight(), 2))
                    ->isEqualTo(118.76)

                ->float(round($computed[$rectangleList[5]]->getHeight()))
                    ->isEqualTo(200)
        ;
    }
}
