<?php
require 'vendor/autoload.php';

use Thomaschatting\ReqresService\v1\UserApiService;

$httpClient = new \GuzzleHttp\Client();
$userApiService = new UserApiService($httpClient);

$userId = 1;
$user = $userApiService->getUserById($userId);

echo "User ID: " . $user->getId() . PHP_EOL;
echo "User Email: " . $user->getEmail() . PHP_EOL;
echo "User First Name: " . $user->getFirstName() . PHP_EOL;
echo "User Last Name: " . $user->getLastName() . PHP_EOL;
echo "User Avatar: " . $user->getAvatar() . PHP_EOL;

$users = $userApiService->getUsers();

print_r($users);

print_r($users->current());
$users->next();
print_r($users->current());
echo "Has more results: " . $users->hasMoreResults();
$users->getNextPage();
print_r($users);