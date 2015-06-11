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
                    ->isInstanceOf('\SplQueue')
                ->integer(count($computed))
                    ->isEqualTo(2)

                ->object($computed[0])
                    ->isInstanceOf('\SplQueue')
                ->integer(count($computed[0]))
                    ->isEqualTo(3)
                ->integer(count($computed[1]))
                    ->isEqualTo(2)


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

                ->object($computed[0])
                    ->isInstanceOf('\SplQueue')
                ->integer(count($computed[0]))
                    ->isEqualTo(3)
                ->integer(count($computed[1]))
                    ->isEqualTo(2)
                ->integer(count($computed[2]))
                    ->isEqualTo(1)
        ;
    }
}
