<?php 

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class WordPressHasher implements HasherContract
{
    protected $hasher;

    public function __construct()
    {
        require_once base_path('app/Helpers/class-phpass.php');
        $this->hasher = new \PasswordHash(8, true);
    }

    public function make($value, array $options = [])
    {
        return $this->hasher->HashPassword($value);
    }

    public function check($value, $hashedValue, array $options = [])
    {
      // شيل البادئة لو موجودة
    if (strpos($hashedValue, '$wp$') === 0) {
        $hashedValue = substr($hashedValue, 4);
    }

    return password_verify($value, $hashedValue);
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        return false; // WordPress hashes don't need rehashing
    }

    public function info($hashedValue)
    {
        return [
            'algo' => 0,
            'algoName' => 'wordpress',
        ];
    }
}
