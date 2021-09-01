# Basic Usage

```php
$url = "https://jsonplaceholder.typicode.com/posts";
$client = new Jordy\Http\Client();

$responseList = $client->transfer(Client::HTTP_GET, $url);

if(! $responseList->isValid()) {
    die("Response not valid");
}

$count = $responseList->count();

$post = $responseList->first();

$id = $post->extractFromBody("id");
$postArray = $post->toArray();
```
