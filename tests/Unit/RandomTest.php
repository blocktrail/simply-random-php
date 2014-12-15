<?php

require_once __DIR__ . '/../MockRandom.php';

class RandomTest extends PHPUnit_Framework_TestCase {

    protected function getSeed() {
        $ret = '';
        for ($i = 0; $i < 32; $i++) {
            $ret .= chr($i);
        }

        return $ret;
    }

    public function testBytes() {
        $mock = new MockRandom($this->getSeed());
        $result = "000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f";
        $result = str_repeat(hex2bin($result), 5);
        for ($i = 1; $i < 132; $i++) {
            $this->assertEquals(substr($result, 0, $i), $mock->bytes($i));
        }
    }

    public function testInt() {
        $mock = new MockRandom($this->getSeed());
        $this->assertTrue(is_int($mock->int(0, 1)));
        for ($i = 0; $i < 256; $i++) {
            // This always works, because the range is a single byte, which is always 0 by our generator
            $this->assertEquals(0, $mock->int(0, $i));
        }
        for ($i = 256; $i < 512; $i++) {
            $this->assertEquals(1, $mock->int(0, $i));
        }
        $this->assertEquals(128, $mock->int(128, 256));
    }

    public function testFloat() {
        $mock = new MockRandom($this->getSeed());
        $this->assertEquals(0.000030757400999616, $mock->float());
    }

    public function testChooseString() {
        $mock = new MockRandom($this->getSeed());
        $this->assertEquals('a', $mock->choose('abcdefghijklmnopqrstuvwxyz'));

    }
}
