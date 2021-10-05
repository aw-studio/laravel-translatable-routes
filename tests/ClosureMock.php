<?php

namespace Tests;

use PHPUnit\Framework\Assert as PHPUnit;

class ClosureMock
{
    /**
     * Determines if the closure has been called.
     *
     * @var bool
     */
    protected $called = false;

    /**
     * Number of closure calls.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * Expected arguemnts.
     *
     * @var array|null
     */
    protected $expectedArguments;

    /**
     * Get closure.
     *
     * @return void
     */
    public function getClosure()
    {
        return function (...$arguments) {
            $this->checkArguments($arguments);
            $this->count++;
            $this->called = true;

            return $this->return;
        };
    }

    /**
     * Assert that closure was called n times.
     *
     * @param  int   $times
     * @return $this
     */
    public function assertWasCalledTimes($times)
    {
        PHPUnit::assertEquals($times, $this->count, 'Failed asserting that closure was called '.$times.' times.');

        return $this;
    }

    /**
     * Assert that closure was called.
     *
     * @return $this
     */
    public function assertWasCalled()
    {
        PHPUnit::assertTrue($this->called, 'Failed asserting that closure was called.');

        return $this;
    }

    /**
     * Set expected arguments.
     *
     * @param  array $parameters
     * @return $this
     */
    public function withArgs(array $arguments)
    {
        $this->expectedArguments = $arguments;

        return $this;
    }

    /**
     * Set return value.
     *
     * @param  mixed $value
     * @return $this
     */
    public function andReturn($value)
    {
        $this->return = $value;

        return $this;
    }

    /**
     * Check expected arguments matches arguments.
     *
     * @param  array $arguments
     * @return void
     */
    protected function checkArguments($arguments)
    {
        if (! $this->expectedArguments) {
            return;
        }

        PHPUnit::assertEquals($this->expectedArguments, $arguments);
    }
}
