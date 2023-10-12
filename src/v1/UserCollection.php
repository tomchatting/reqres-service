<?php
namespace Thomaschatting\ReqresService\v1;

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use Iterator;
use JsonSerializable;

class UserCollection implements Iterator, JsonSerializable {
 
    private $users = [];
    private $position = 0;
    private $currentPage = 1;
    private $totalPages = 1;

    public function __construct(array $users, $currentPage = 1, $totalPages = 1) {
        $this->users = $users;
        $this->position = 0;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
    }

    public function getNextPage() {
        if ($this->hasMoreResults()) {
            $newPage = $this->currentPage + 1;
            $httpClient = new \GuzzleHttp\Client();
            $userApiService = new UserApiService($httpClient);

            $newUserData = $userApiService->getUsers($newPage);

            $this->currentPage = $newPage;
            $this->position = 0;

            $this->users = [];

            array_push($this->users, $newUserData);
        }
    }

    public function hasMoreResults() {
        return $this->currentPage < $this->totalPages;
    }

    public function current() : User {
        return $this->users[$this->position];
    }

    public function key() : int {
        return $this->position;
    }

    public function next() : void {
        ++$this->position;
    }

    public function rewind() : void {
        $this->position = 0;
    }

    public function valid() : bool {
        return isset($this->users[$this->position]);
    }

    public function jsonSerialize() : array {
        return $this->users;
    }
}

?>