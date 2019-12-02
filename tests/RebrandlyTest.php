<?php

namespace Bybrand\ShortenerURL\Test;

use PHPUnit\Framework\TestCase;

use Bybrand\ShortenerURL\Provider\Rebrandly;
use Bybrand\ShortenerURL\Shorten;
use Bybrand\ShortenerURL\Exception\ShortenFailed;

use Mockery as m;

/**
 * @group Rebrandly
 */
class RebrandlyTest extends TestCase
{
    protected $provider;

    /**
     * @before
     */
    public function setupBefore()
    {
        // Generator fake contents.
        $this->faker = \Faker\Factory::create();

        $this->provider = new Rebrandly([
            'workspace' => $this->faker->domainWord,
            'domain'    => $this->faker->domainName,
            'apikey'    => $this->faker->md5,
        ]);
    }

    /**
     * @after
     */
    public function downAfter()
    {
        m::close();
    }

    /**
     * @group Rebrandly
     * @group Rebrandly.destinationNotEmply
     */
    public function testDestinationNotEmplyException()
    {
        $this->expectException(ShortenFailed::class);
        $this->expectExceptionMessage('The long URL not be empty');

        $shorten = new Shorten($this->provider);
        $shorten->destination('');
        $shorten->create();
    }

    /**
     * @group Rebrandly
     * @group Rebrandly.shorten
     */
    public function testShorten()
    {
        $jsonResult = [
            'shortUrl'    => $this->faker->domainName . '/' . $this->faker->userName,
            'id'          => $this->faker->md5,
            'destination' => $this->faker->url,
            'title'       => ''
        ];

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($jsonResult));
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $HTTPClientMock = m::mock('GuzzleHttp\ClientInterface');
        $HTTPClientMock->shouldReceive('post')->times(1)->andReturn($response);

        // Set custom HTTP client.
        $this->provider->setHttpClient($HTTPClientMock);

        $shorten = new Shorten($this->provider);
        $shorten->destination($this->faker->url);
        $shorten->create();

        // Get all returnet params.
        $result = $shorten->toArray();

        $this->assertArrayHasKey('shortUrl', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('destination', $result);
    }

    /**
     * @group Rebrandly
     * @group Rebrandly.brandedLink
     */
    public function testBrandedLink()
    {
        $jsonResult = [
            'shortUrl'    => $this->faker->domainName . '/bybrand',
            'id'          => $this->faker->md5,
            'destination' => $this->faker->url,
            'slashtag'    => 'bybrand'
        ];

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($jsonResult));
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $HTTPClientMock = m::mock('GuzzleHttp\ClientInterface');
        $HTTPClientMock->shouldReceive('post')->times(1)->andReturn($response);

        // Set custom HTTP client.
        $this->provider->setHttpClient($HTTPClientMock);

        $shorten = new Shorten($this->provider);
        $shorten->destination($this->faker->url);
        $shorten->withBranded('bybrand');
        $shorten->create();

        // Get all returnet params.
        $result = $shorten->toArray();

        $this->assertArrayHasKey('shortUrl', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('destination', $result);
        $this->assertArrayHasKey('slashtag', $result);
    }

    /**
     * @group Rebrandly
     * @group Rebrandly.methods
     */
    public function testMethods()
    {
        $jsonResult = [
            'shortUrl'    => $this->faker->domainName . '/' . $this->faker->userName,
            'id'          => $this->faker->md5,
            'destination' => $this->faker->url,
            'title'       => '',
            'slashtag'    => 'bybrand'
        ];

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($jsonResult));
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $HTTPClientMock = m::mock('GuzzleHttp\ClientInterface');
        $HTTPClientMock->shouldReceive('post')->times(1)->andReturn($response);

        // Set custom HTTP client.
        $this->provider->setHttpClient($HTTPClientMock);

        $shorten = new Shorten($this->provider);
        $shorten->destination($this->faker->url);
        $shorten->create();

        $this->assertEquals($jsonResult['shortUrl'], $shorten->getLink());
        $this->assertEquals($jsonResult['id'], $shorten->getId());
        $this->assertEquals($jsonResult['destination'], $shorten->getDestination());
    }
}
