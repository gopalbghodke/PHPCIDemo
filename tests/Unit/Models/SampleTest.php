<?php

namespace Brunoocto\Sample\Tests\Unit\Models;

use Brunoocto\Sample\Models\Sample;
use Brunoocto\Sample\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SampleTest extends TestCase
{
    // Trait which does rollback to initial status any database modified during testing
    use RefreshDatabase;

    /**
     * Example of database insertion
     *
     * @return void
     */
    public function testCreateASample()
    {
        $count = Sample::count();
        
        $item = factory(Sample::class)->create();

        // Check that that databe contains refreshly created text
        $this->assertDatabaseHas($item->getTable(), [
            'text' => $item->text,
        ]);

        // Check that the database has one more insert
        $this->assertCount($count+1, Sample::all());
    }
}
