<?php

namespace Brunoocto\Sample\Tests\Feature;

use Brunoocto\Sample\Tests\TestCase;

class SampleFeatureTest extends TestCase
{
    /**
     * Some checks that can be widely reused
     *
     * @return void
     */
    protected function commonCheck($response)
    {
        // Check the Status (2xx)
        $response->assertSuccessful();
        
        // Check the type of answer
        $response->assertHeader('Content-Type', 'application/vnd.api+json');
        
        // Check the Body structure
        $response->assertJsonStructure([
            'data',
            'errors',
            'meta' => [
                'message',
            ],
        ]);
    }

    /**
     * Test the dependency injection
     *
     * @return void
     */
    public function testUseDependencyInjection()
    {
        // Build the request
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/dependency-injection',
            [],
            ['Content-Type' => 'application/json',]
        );
        
        $this->commonCheck($response);

        // Check if the json contains some value
        $response->assertJson([
            'meta' => [
                'binding' => true,
            ],
        ]);

        //Check if the Body contains a String
        $response->assertSeeText('Dependency injection');
    }

    /**
     * Test the interface
     *
     * @return void
     */
    public function testUseInterface()
    {
        // Build the request
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/interface',
            [],
            ['Content-Type' => 'application/json',]
        );
        
        $this->commonCheck($response);

        // Check if the json contains some value
        $response->assertJson([
            'meta' => [
                'binding' => true,
            ],
        ]);

        //Check if the Body contains a String
        $response->assertSeeText('Interface Dependency injection');
    }

    /**
     * Test the facade
     *
     * @return void
     */
    public function testUseFacade()
    {
        // Build the request
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/facade',
            [],
            ['Content-Type' => 'application/json',]
        );
        
        $this->commonCheck($response);

        // Check if the json contains some value
        $response->assertJson([
            'meta' => [
                'binding' => true,
            ],
        ]);

        //Check if the Body contains a String
        $response->assertSeeText('Facade with Interface');
    }

    /**
     * Test the maker
     *
     * @return void
     */
    public function testUseMaker()
    {
        // Build the request
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/maker',
            [],
            ['Content-Type' => 'application/json',]
        );
        
        $this->commonCheck($response);

        // Check if the json contains some value
        $response->assertJson([
            'meta' => [
                'binding' => true,
            ],
        ]);

        //Check if the Body contains a String
        $response->assertSeeText('Maker with Interface');
    }

    /**
     * Test the maker
     *
     * @return void
     */
    public function testRunATest()
    {
        // Build the request
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/test',
            [],
            ['Content-Type' => 'application/json',]
        );
        
        $this->commonCheck($response);

        // Check if the json contains some value
        $response->assertJson([
            'meta' => [
                'binding' => false,
            ],
        ]);

        //Check if the Body contains a String
        $response->assertSeeText('Controller for test only');
    }

    /**
     * Test the maker
     *
     * @return void
     */
    public function testCreateASample()
    {
        // Build the request
        $response = $this->json(
            'POST',
            '/brunoocto/sample/samples',
            [
                'data' => [
                    'type' => 'sample',
                    'attributes' => [
                        'text' => 'Some text'
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/json',
            ]
        );
        
        $this->commonCheck($response);

        // Check if the json contains some value
        $response->assertJson([
            'meta' => [
                'binding' => true,
            ],
        ]);
        
        //Check if the Body contains a String
        $response->assertSeeText('Create Sample');
    }
}
