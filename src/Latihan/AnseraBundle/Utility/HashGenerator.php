<?php
namespace Latihan\AnseraBundle\Utility;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("lat.utility.hash_generator")
 */
class HashGenerator
{
    public function generateRandomHash($hash='sha256') {
        $salt = $this->generateRandomSalt();
        return hash($hash, $salt);
    }

    /**
     * Generates a random string of characters
     *
     * Used to salt hashes
     */
    public function generateRandomSalt($validCharacters="abcdefghijklmnopqrstuvwxyz0123456789", $length=25) {
        $randomString = "";
        $validCharacterCount = strlen($validCharacters);

        for ($i = 0; $i < $length; $i++)
        {
            // pick a random number from 1 up to the number of valid chars
            $charIndex = mt_rand(1, $validCharacterCount) - 1;
            $randomString .= $validCharacters[$charIndex];
        }

        return $randomString;
    }
}