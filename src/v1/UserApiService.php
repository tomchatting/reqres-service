<?php
namespace Thomaschatting\ReqresService\v1;

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

class UserApiService {
    private $httpClient;

    public function __construct(Client $httpClient) {
        $this->httpClient = $httpClient;
    }

    public function getUserById($userId): ?User {
        $apiEndpoint = 'https://reqres.in/api/users/' . $userId;

        try {
            $request = $this->httpClient->get($apiEndpoint);
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);

            if (isset($data['data'])) {
                $user = User::create(
                    $data['data']['id'],
                    $data['data']['email'],
                    $data['data']['first_name'],
                    $data['data']['last_name'],
                    $data['data']['avatar']
                );

                return $user;
            }

            return null;
        } catch (GuzzleException $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return null;
        }
    }

    public function createUser($name, $job): ?int {
        $apiEndpoint = 'https://reqres.in/api/users/';

        try {
            $request = $this->httpClient->post($apiEndpoint, [
                'json' => ['name' => $name, 'job' => $job]
            ]);

            $response = $request->getBody();
            $body = $response->getContents();

            $data = json_decode($body, true);

            if (isset($data['id'])) {
                return $data['id'];
            }

            return null;
        } catch (GuzzleException $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return null;
        }
    }

    public function getUsers($page = 1): ?UserCollection {
        $apiEndpoint = 'https://reqres.in/api/users/?page=' . $page;

        try {
            $response = $this->httpClient->get($apiEndpoint);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (isset($data['data'])) {
                $userData = $data['data'];

                $users = [];
                foreach ($userData as $userDataItem) {
                    $users[] = User::create(
                        $userDataItem['id'],
                        $userDataItem['email'],
                        $userDataItem['first_name'],
                        $userDataItem['last_name'],
                        $userDataItem['avatar']
                    );
                }

                $totalPages = $data['total_pages'];
                $currentPage = $data['page'];

                return new UserCollection($users, $currentPage, $totalPages);
            }

            return null;
        } catch (GuzzleException $e) {
            // Handle the HTTP request error, log it, or rethrow it as needed.
            // You can also return a specific error response here.
            return null;
        }
    }
    
}

?>