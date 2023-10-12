<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Thomaschatting\ReqresService\v1\User;
use Thomaschatting\ReqresService\v1\UserApiService;
use Thomaschatting\ReqresService\v1\UserCollection;
use Psr\Http\Message\StreamInterface;

class UserApiServiceTest extends TestCase {
    protected $userApiService;
    protected $httpClient;
    
    public function setUp(): void {
        $this->httpClient = m::mock(\GuzzleHttp\Client::class);
        $this->userApiService = new UserApiService($this->httpClient);
    }

    public function tearDown(): void {
        m::close();
    }

    public function testGetUsers() {
        $userData = [
            ['id' => 1, 'email' => 'john.smith@example.com', 'first_name' => 'John', 'last_name' => 'Doe', 'avatar' => 'https://reqres.in/img/faces/1-image.jpg'],
            ['id' => 2, 'email' => 'jane.doe@example.com', 'first_name' => 'Jane', 'last_name' => 'Smith', 'avatar' => 'https://reqres.in/img/faces/2-image.jpg'],
        ];

        $stream = m::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')->andReturn(json_encode(['data' => $userData]));

        $this->httpClient->shouldReceive('get')->once()
            ->with('https://reqres.in/api/users/?page=1')
            ->andReturn(m::mock('Psr\Http\Message\ResponseInterface', ['getBody' => $stream]));

        $userCollection = $this->userApiService->getUsers();

        $this->assertInstanceOf(UserCollection::class, $userCollection);
        $this->assertCount(2, $userCollection);
        $this->assertInstanceOf(User::class, $userCollection->current());
    }

    public function testGetUserById() {
        $userId = 1;
    
        $userData = ['id' => $userId, 'email' => 'george.bluth@reqres.in', 'first_name' => 'George', 'last_name' => 'Bluth', 'avatar' => 'https://reqres.in/img/faces/1-image.jpg'];
    
        $stream = m::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')->andReturn(json_encode(['data' => $userData]));
    
        $this->httpClient->shouldReceive('get')->once()
            ->with("https://reqres.in/api/users/{$userId}")
            ->andReturn(m::mock('Psr\Http\Message\ResponseInterface', ['getBody' => $stream]));
    
        $user = $this->userApiService->getUserById($userId);
    
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['id'], $user->getId());
        $this->assertEquals($userData['email'], $user->getEmail());
        $this->assertEquals($userData['first_name'], $user->getFirstName());
        $this->assertEquals($userData['last_name'], $user->getLastName());
        $this->assertEquals($userData['avatar'], $user->getAvatar());
    }

    public function testCreateUser() {
        $userData = [
            'id' => 123,
            'name' => 'John Doe',
            'job' => 'Developer',
        ];
    
        $stream = m::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')->andReturn(json_encode($userData));
    
        $this->httpClient->shouldReceive('post')->once()
            ->with('https://reqres.in/api/users/', [
                'json' => ['name' => $userData['name'], 'job' => $userData['job']]
            ])
            ->andReturn(m::mock('Psr\Http\Message\ResponseInterface', ['getBody' => $stream]));
    
        $userId = $this->userApiService->createUser($userData['name'], $userData['job']);
    
        $this->assertEquals(123, $userId);
    }

    public function testGetNextPage() {
        
        $page1Data = [
            ['id' => 1, 'email' => 'christopher.farmer@example.com', 'first_name' => 'Christopher', 'last_name' => 'Farmer', 'avatar' => 'https://reqres.in/img/faces/1-image.jpg'],
            ['id' => 2, 'email' => 'jane.doe@example.com', 'first_name' => 'Jane', 'last_name' => 'Smith', 'avatar' => 'https://reqres.in/img/faces/2-image.jpg'],
        ];

        $page2Data = [
            ['id' => 3, 'email' => 'john.smith@example.com', 'first_name' => 'John', 'last_name' => 'Doe', 'avatar' => 'https://reqres.in/img/faces/1-image.jpg'],
        ];

        $userApiService = m::mock(UserApiService::class);
        $userApiService->shouldReceive('getUsers')
            ->with(2)
            ->andReturn(new UserCollection($page2Data, 2, 2));

        $userCollection = new UserCollection($page1Data, 1, 2);

        $this->assertEquals(count($page1Data), 2);
        
        $userCollection->getNextPage($userApiService);

        $this->assertEquals(count($page2Data), 1);
    }

}

?>