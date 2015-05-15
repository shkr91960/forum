<?php
/*
    This class is responsible for the password hashing.
*/

class vazipasswordhash {
    const PBKDF2_HASH_ALGORITHM = "sha256";
    const PBKDF2_ITERATIONS = 1000;
    const PBKDF2_SALT_BYTE_SIZE = 24;
    const PBKDF2_HASH_BYTE_SIZE = 24;

    const HASH_SECTIONS = 2;
    //const HASH_ALGORITHM_INDEX = 0;
    //const HASH_ITERATION_INDEX = 1;
    const HASH_SALT_INDEX = 0;
    const HASH_PBKDF2_INDEX = 1;

    public function create_hash($password) {
        // format: /*algorithm:iterations:*/salt:hash
        $salt = base64_encode(mcrypt_create_iv(self::PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
        return /*self::PBKDF2_HASH_ALGORITHM . ":" . self::PBKDF2_ITERATIONS . ":" .*/ $salt . ":" . 
            base64_encode($this->pbkdf2(
                self::PBKDF2_HASH_ALGORITHM,
                $password,
                $salt,
                self::PBKDF2_ITERATIONS,
                self::PBKDF2_HASH_BYTE_SIZE,
                true
            ));
    }

    public function validate_password($password, $correct_hash) {
        $params = explode(":", $correct_hash);
        if(count($params) < self::HASH_SECTIONS)
           return false; 
        $pbkdf2 = base64_decode($params[self::HASH_PBKDF2_INDEX]);
        return $this->slow_equals(
            $pbkdf2,
            $this->pbkdf2(
                self::PBKDF2_HASH_ALGORITHM,
                $password,
                $params[self::HASH_SALT_INDEX],
                (int)self::PBKDF2_ITERATIONS,
                strlen($pbkdf2),
                true
            )
        );
    }

    private function slow_equals($a, $b) {
        $diff = strlen($a) ^ strlen($b);
        for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
        {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $diff === 0; 
    }

    private function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false) {
        $algorithm = strtolower($algorithm);
        if(!in_array($algorithm, hash_algos(), true))
            trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
        if($count <= 0 || $key_length <= 0)
            trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);

        if (function_exists("hash_pbkdf2")) {
            // The output length is in NIBBLES (4-bits) if $raw_output is false!
            if (!$raw_output) {
                $key_length = $key_length * 2;
            }
            return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
        }

        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = ceil($key_length / $hash_length);

        $output = "";
        for($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if($raw_output)
            return substr($output, 0, $key_length);
        else
            return bin2hex(substr($output, 0, $key_length));
    }

}

?>