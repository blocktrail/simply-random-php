<?php

class MockRandom extends BlockTrail\SimplyRandom\Random {
    protected $seed;

    public function __construct($seed) {
        $this->seed = $seed;
    }

    protected function genRandom($length) {
        $buffer = str_repeat($this->seed, ceil($length / strlen($this->seed)));
        return substr($buffer, 0, $length);
    }
}
