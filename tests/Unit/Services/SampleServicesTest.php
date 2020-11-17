<?php

namespace Brunoocto\Sample\Tests\Unit\Services;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Services\SampleService;

class SampleServicesTest extends TestCase
{
    /**
     * Test the format returned by the method "json"
     *
     * @return void
     */
    public function testJsonResponse()
    {
        // Check the type
        $sample = new SampleService;
        $json = $sample->json('test');
        $this->assertEquals('Illuminate\Http\JsonResponse', get_class($json));

        // Check the response is well formated
        $response = json_decode($json->content());
        $this->assertEquals('test', $response->data->{0});
        $this->assertEquals(false, $response->meta->binding);

        //Check the bind feature works
        $sample->bind();
        $json = $sample->json('test');
        $response = json_decode($json->content());
        $this->assertEquals(true, $response->meta->binding);
    }

    /**
     * Test the format returned by the method "json"
     *
     * @return void
     */
    public function testDotenv()
    {
        $this->assertEquals(false, env('SAMPLE_DB_FOREIGN_KEYS'));
    }
}
