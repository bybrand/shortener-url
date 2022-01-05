<?php

namespace Bybrand\ShortenerURL\Provider;

use Bybrand\ShortenerURL\ShortenInterface;
use Bybrand\ShortenerURL\Tool\GuardedPropertyTrait;
use Bybrand\ShortenerURL\Tool\ArrayAccessorTrait;
use Bybrand\ShortenerURL\Exception;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class Rebrandly implements ShortenInterface
{
    use GuardedPropertyTrait;
    use ArrayAccessorTrait;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    private $longUrl;
    private $response = [];

    /**
     * The target URL endpoint to short link.
     * @var string
     */
    protected $targetEndpoint = 'https://api.rebrandly.com/v1/links';

    /**
     * The base domain to url shortned.
     * @var string
     */
    protected $domain = 'rebrand.ly';

    /**
     * A Rebrandly ID, get in Rebrandly dashboard.
     * @var string
     */
    protected $workspace;

    /**
     * The API key specification, get in Rebrandly dashboard.
     * @var string
     */
    protected $apikey;

    public function __construct(array $options = [])
    {
        // We'll let the GuardedPropertyTrait handle mass assignment of incoming
        // options.
        $this->fillProperties($options);

        $this->setHttpClient(new HttpClient);
    }

    /**
     * Sets the HTTP client instance.
     *
     * @param  HttpClientInterface $client
     * @return self
     */
    public function setHttpClient(HttpClientInterface $client)
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * Returns the HTTP client instance.
     *
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function destination($url)
    {
        $this->longUrl = $url;
    }

    public function post()
    {
        if (!$this->longUrl) {
            throw new Exception\ShortenFailed('The long URL not be empty.');
        }

        try {
            // The Guzzle Client.
            $response = $this->httpClient->post($this->targetEndpoint, [
                'json' => [
                    'domain'      => ['fullName' => $this->domain],
                    'destination' => $this->longUrl
                ],
                'headers' => [
                    'apikey'       => $this->apikey,
                    'workspace'    => $this->workspace,
                    'Content-Type' => 'application/json',
                ]
            ]);
            // Return success.
            $body = (string) $response->getBody();

            // Save all params returned in array.
            $this->response = json_decode($body, true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                throw new Exception\ShortenFailed(
                    Psr7\Message::toString($e->getResponse())
                );
            }
            throw new Exception\ShortenFailed('The URL destination does not shortened.');
        }
    }

    public function getLink()
    {
        return $this->getValueByKey($this->response, 'shortUrl');
    }

    public function getId()
    {
        return $this->getValueByKey($this->response, 'id');
    }

    public function getDestination()
    {
        return $this->getValueByKey($this->response, 'destination');
    }

    /**
     * Return all of the params details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
