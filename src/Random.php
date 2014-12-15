<?php

namespace BlockTrail\SimplyRandom;

class Random
{
    const TOKEN_LOWER_ALPHA = "abcdefghijklmnopqrstuvwxyz";
    const TOKEN_UPPER_ALPHA = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const TOKEN_ALPHA = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const TOKEN_ALNUM = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    /**
     * @var Random
     */
    protected static $instance;

    protected $devurandomFallback;

    protected $opensslFallback;

    /**
     * @param bool $devurandomFallback  enable fallback to /dev/urandom
     * @param bool $opensslFallback     enable fallback to openssl_random_pseudo_bytes
     */
    public function __construct($devurandomFallback = true, $opensslFallback = false)
    {
        $this->devurandomFallback = $devurandomFallback;
        $this->opensslFallback = $opensslFallback;
    }

    /**
     * Return instance of Random with default settings
     *
     * @return Random
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Random(true, false);
        }

        return self::$instance;
    }

    /**
     * Generate a native binary string
     *
     * @param int $length The length of the string to generate
     *
     * @return string The generated string
     */
    public function bytes($length)
    {
        return $this->genRandom($length);
    }

    /**
     * Generate a random integer
     *
     * @param int $min The starting point, or if max is omitted the ending point
     * @param int $max The ending point
     *
     * @return int The generated integer
     */
    public function int($min, $max = null)
    {
        if (is_null($max)) {
            $max = $min;
            $min = 0;
        }
        $range = (int)($max - $min);
        $bits = $this->countBits($range) + 1;
        $bytes = (int)max(ceil($bits / 8), 1);
        $mask = pow(2, $bits) - 1;
        if ($mask >= \PHP_INT_MAX) {
            $mask = \PHP_INT_MAX;
        } else {
            $mask = (int)$mask;
        }
        do {
            $test = $this->bytes($bytes);
            $result = hexdec(bin2hex($test)) & $mask;
        } while ($result > $range);

        return $result + $min;
    }

    /**
     * Generate a random floating point number between 0 and 1.0
     *
     * @return float The generated float
     */
    public function float()
    {
        return ($this->int(0, \PHP_INT_MAX) / (\PHP_INT_MAX));
    }

    /**
     * Given a sequence (string, array or object), pick one of them at random
     *
     * @param string|array|object $value The sequence to choose from
     *
     * @return mixed The randomly chosen value
     */
    public function choose($value)
    {
        if (is_string($value)) {
            $count = strlen($value);
        } elseif (is_array($value)) {
            $value = array_values($value);
            $count = count($value);
        } elseif (is_object($value)) {
            $value = array_values(get_object_vars($value));
            $count = count($value);
        } else {
            throw new \InvalidArgumentException('Unsure what to choose from, please provide a string, array or object');
        }
        return $value[$this->int(0, $count - 1)];
    }

    /**
     * Given a sequence(string, array or object), generate a shuffled sequence
     * Note: for strings, the return is a string. For arrays and objects, it's an array
     *
     * @param string|array|object $value The sequence to choose from
     *
     * @return string|array The generated shuffled sequence
     */
    public function shuffle($value)
    {
        if (is_string($value)) {
            $buffer = str_split($value, 1);
        } elseif (is_array($value)) {
            $buffer = array_values($value);
        } elseif (is_object($value)) {
            $buffer = array_values(get_object_vars($value));
        } else {
            throw new \InvalidArgumentException('Unsure what to shuffle, please provide a string, array or object');
        }
        $result = array();
        $length = count($buffer);
        while (!empty($buffer)) {
            $seed = $this->genRandom(max(32, $length));
            do {
                $bits = $this->countBits(count($buffer));
                $bytesNeeded = max(ceil($bits / 8), 1);
                $mask = (int)(pow(2, $bits) - 1);
                $stub = hexdec(bin2hex(substr($seed, 0, $bytesNeeded))) & $mask;
                $seed = substr($seed, $bytesNeeded);
                if (isset($buffer[$stub])) {
                    $result[] = $buffer[$stub];
                    unset($buffer[$stub]);
                    $buffer = array_values($buffer);
                }
            } while (isset($seed[$bytesNeeded]));
        }
        if (is_string($value)) {
            return implode('', $result);
        }
        return $result;
    }

    /**
     * Generate a token with the specified alphabet
     *
     * @param int          $length   The length of the token to generate
     * @param string|array $alphabet The "alphabet" (characters) to use
     *
     * @return string The generated random token
     */
    public function token($length, $alphabet = self::TOKEN_ALNUM)
    {
        if (is_string($alphabet)) {
            $alphabet = str_split($alphabet, 1);
        } elseif (is_array($alphabet)) {
            $alphabet = array_values($alphabet);
        } else {
            throw new \InvalidArgumentException("Expecting either a String or Array alphabet");
        }
        $alphabet = array_unique($alphabet);
        $count = count($alphabet);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $alphabet[$this->int(0, $count - 1)];
        }
        return $result;
    }

    /**
     * Protected function to overload the generator for unit testing purposes
     *
     * @param $length
     * @return string
     */
    protected function genRandom($length)
    {
        if (function_exists('mcrypt_create_iv')) {
            $random = mcrypt_create_iv($length, \MCRYPT_DEV_URANDOM);

            if (strlen($random)) {
                return $random;
            }
        }

        if (self::DEV_URANDOM_FALLBACK && file_exists('/dev/urandom') && is_readable('/dev/urandom')) {
            $random = file_get_contents('/dev/urandom', false, null, -1, $length);

            if (strlen($random)) {
                return $random;
            }
        }

        if (self::OPENSSL_FALLBACK && function_exists('openssl_random_pseudo_bytes')) {
            $random = openssl_random_pseudo_bytes($length, $strength);

            if ($strength) {
                return $random;
            }
        }

        throw new \LogicException('Could not generate secure random number');
    }

    /**
     * Count the number of bits needed to represent an integer
     *
     * @param $number
     * @return int
     */
    protected function countBits($number)
    {
        $log2 = 0;
        while ($number >>= 1) {
            $log2++;
        }
        return $log2;
    }
}
