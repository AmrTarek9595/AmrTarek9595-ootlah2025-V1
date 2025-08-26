<?php

return [

    'default' =>  'wordpress',

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 8),
    ],

    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
    ],

];
