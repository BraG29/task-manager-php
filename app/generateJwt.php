<?php

use App\Interface\Dtos\UserDTO;
use Firebase\JWT\JWT;

return function (UserDTO $userDTO){

    $config = file_get_contents(__DIR__ . "/../config.json");
    $config = json_decode($config, true);

    $secretKey = $config['api']['JWT_SECRET'];
    $payload = [
        'iat' => time(),
        'exp' => time() + 3600,
        'uid' => $userDTO->getId(),
        'name' => $userDTO->getName(),
        'lastName' => $userDTO->getLastName()
    ];

    return JWT::encode($payload, $secretKey, 'HS256');
};