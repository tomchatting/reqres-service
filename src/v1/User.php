<?php
namespace Thomaschatting\ReqresService\v1;

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use JsonSerializable;

class User implements JsonSerializable {

    private function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $avatar
    ) {}

    public static function create(int $id, string $email, string $firstName, string $lastName, string $avatar): User {
        return new self($id, $email, $firstName, $lastName, $avatar);
    }

    public function getId() : int {
        return $this->id;
    }

    public function getEmail() : string {
        return $this->email;
    }

    public function getFirstName() : string {
        return $this->firstName;
    }

    public function getLastName() : string {
        return $this->lastName;
    }

    public function getAvatar() : string {
        return $this->avatar;
    }

    public function jsonSerialize() : array {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'avatar' => $this->avatar,
        ];
    }

    public static function fromArray(array $data) : User {
        return new self(
            $data['id'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['avatar']
        );
    }

    public function toArray() : array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'avatar' => $this->avatar,
        ];
    }

}

?>