# Extending

- Map available objects and fields
- No need to look up documentation
- Easy accessibility of fields

### Example
```php
$client = (new Jordy\Http\Client())
    ->setHeaders([
        "Content-Type" => "application/json"
    ]);
    
$api = new JsonPlaceholderApi($client);

$responseList = $api->posts()->getAll();

if(! $responseList->isValid()) {
    die("ResponseList not valid. Statuscode: " . $responseList->getStatusCode());
}

$responseList->count();
$responseItem = $responseList->first(); 

$post = new Post($responseItem->toArray());
$post->body = "Lorem ipsum...";

$saveResponse = $api->posts()->save($post);
if(! $saveResponse->isValid()) {
    die("Save Response not valid. Statuscode: " . $saveResponse->getStatusCode());
}

// Pass the Post object as a Filter
$filterResponseList = $api->posts()->getAll($post);
if(! $filterResponseList->isValid()) {
    die("FilterResponseList not valid. Statuscode: " . $filterResponseList->getStatusCode());
}

$id = $filterResponseList->first()->getId();

if($id) {
    $deleteResponse = $api->posts()->delete($id);
    if(! $deleteResponse->isValid()) {
        die("Delete Response not valid. Statuscode: " . $deleteResponse->getStatusCode());
    }
}
```

### Entity
An entity object maps all the available filters or fields

```php
class Post
{
    public $id;
    public $userId;
    public $title;
    public $body;

    /**
     * Post constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach($data as $property => $value) {
            if(property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "id" => $this->id,
            "userId" => $this->userId,
            "title" => $this->title,
            "body" => $this->body,
        ];
    }
}
```

### Response
A response object maps all the available fields on the returned object

```php
use Jordy\Http\Response;

class PostResponse extends Response
{
    public function getId()
    {
        return $this->extractFromBody("id");
    }

    public function getUserId()
    {
        return $this->extractFromBody("userId");
    }

    public function getTitle()
    {
        return $this->extractFromBody("title");
    }

    public function getBody()
    {
        return $this->extractFromBody("body");
    }
}
```

### Endpoint
An endpoint object maps all the available options for a specific entity

```php

use Jordy\Http\Client;
use Jordy\Http\ClientInterface;
use Jordy\Http\ResponseInterface;

class PostEndpoint extends AbstractEndpoint
{
    protected $uri = "https://jsonplaceholder.typicode.com/posts";
    protected $useResponseList = false;

    /**
     * PostEndpoint constructor.
     *
     * @param ClientInterface        $client
     * @param ResponseInterface|null $responsePrototype
     */
    public function __construct(
        ClientInterface $client,
        ResponseInterface $responsePrototype = null
    ) {
        parent::__construct(
            $client,
            $responsePrototype ?? new PostResponse()
        );
    }

    /**
     * @param $id
     *
     * @return ResponseInterface
     */
    public function getById($id)
    {
        return $this
            ->withUri("{$this->uri}/{$id}")
            ->transfer(Client::HTTP_GET);
    }

    /**
     * @param Post|null $postFilter
     *
     * @return ResponseInterface
     */
    public function getAll(Post $postFilter = null)
    {
        return $this
            ->withQueryParams($postFilter ? $postFilter->toArray() : [])
            ->returnResponseList()
            ->transfer(Client::HTTP_GET);
    }

    /**
     * @param Post $post
     *
     * @return ResponseInterface
     */
    public function save(Post $post)
    {
        return $this
            ->withPostBody($post->toArray())
            ->transfer(Client::HTTP_POST);
    }

    /**
     * @param Post $post
     *
     * @return ResponseInterface
     */
    public function update(Post $post)
    {
        return $this
            ->withUri("{$this->uri}/{$post->getId()}")
            ->withPostBody($post->toArray())
            ->transfer(Client::HTTP_PUT);
    }

    /**
     * @param $id
     *
     * @return ResponseInterface
     */
    public function delete($id)
    {
        return $this
            ->withUri("{$this->uri}/$id")
            ->transfer(Client::HTTP_DELETE);
    }
}
```

### Api
An Api object to wrap all the available endpoints together

```php
use Jordy\Http\ClientInterface;

class JsonPlaceholderApi
{
    private $client;
    
    private $postEndpoint;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }
    
    public function posts()
    {
        if(! $this->postEndpoint) {
            $this->postEndpoint = new PostEndpoint($this->client);
        }
        
        return $this->postEndpoint;
    }
}
```
