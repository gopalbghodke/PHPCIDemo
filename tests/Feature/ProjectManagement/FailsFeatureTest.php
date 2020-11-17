<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Models\ProjectManagement\FailsAdapter;
use Brunoocto\Sample\Tests\TestCase;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Support\Collection;
use Brunoocto\Sample\Models\ProjectManagement\Fails;

class FailsAdapaterTest extends FailsAdapter
{
    /**
     * This method exist to test protected methods
     */
    public function testProtected()
    {
        $filters = new Collection;
        $fail = factory(Fails::class)->create();
        $query = $fail->where('id', $fail->id);
        return $this->filter($query, $filters);
    }
}

class FailsFeatureTest extends TestCase
{
    /**
     * Test that the Resolve return an error
     *
     * @return void
     */
    public function testResolver()
    {
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/fails',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check that an expection is sent
        $this->assertTrue(isset($response->exception));
    }

    /**
     * Test that the Resolve return an error
     *
     * @return void
     */
    public function testAdapter()
    {
        $paging = app()->make(StandardStrategy::class);
        $adapter = new FailsAdapaterTest($paging);
        // Test protected methods
        $test = $adapter->testProtected();
        // A void function should return Null
        $this->assertNull($test);
    }
}
