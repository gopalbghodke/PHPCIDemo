<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\Details;
use Brunoocto\Sample\Models\ProjectManagement\NonLinears;
use Brunoocto\Sample\Models\ProjectManagement\Projects;

class DetailsFeatureTest extends TestCase
{
    /**
     * Test to create a Details
     *
     * @return void
     */
    public function testDetailsPost()
    {
        // Create a fake File for relationships
        $non_linear = factory(NonLinears::class)->create();

        // Set some variables to be checked
        $content = 'Léo 马丁';

        // Success
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/details',
            [
                'data' => [
                    'type' => 'details',
                    'attributes' => [
                        'title' => $content,
                    ],
                    'relationships' => [
                        'non_linears' => [
                            'data' => [
                                'type' => 'non_linears',
                                'id' => $non_linear->id,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Creation status
        $response->assertStatus(201);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'details',
                'attributes' => [
                    'title' => $content,
                ],
                'relationships' => [
                    'non_linears' => [
                        'data' => [
                            'type' => 'non_linears',
                            'id' => $non_linear->id,
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ]
                    ],
                ],
            ],
        ]);

        // Check the structure
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'created_by',
                    'updated_by',
                    'deleted_by',
                    'title'
                ],
                'relationships' => [
                    'non_linears' => [
                        'data' => [
                            'type',
                            'id',
                        ],
                        'meta' => [
                            'total',
                            'editable',
                        ],
                        'links' => [
                            'self',
                            'related',
                        ],
                    ],
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);
    }

    /**
     * Test to fail creating a Details
     *
     * @return void
     */
    public function testDetailsPostFail()
    {
        // Create a fake File for relationships
        $project = factory(Projects::class)->create();

        // Set some variables to be checked
        $content = 'Léo 马丁';

        // Fail (missing relationships)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/details',
            [
                'data' => [
                    'type' => 'details',
                    'attributes' => [
                        'title' => $content,
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check that an expection is sent
        $this->assertTrue(isset($response->exception));

        // Fail (unauthorized relation)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/details',
            [
                'data' => [
                    'type' => 'details',
                    'attributes' => [
                        'title' => $content,
                    ],
                    'relationships' => [
                        'projects' => [
                            'data' => [
                                [
                                    'type' => 'projects',
                                    'id' => $project->id,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
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
     * Test to get multiple details
     *
     * @return void
     */
    public function testDetailsAllGet()
    {
        $count_ori = Details::count();
        // Then create 2 more fake items
        factory(Details::class)->create();
        factory(Details::class)->create();
        
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/details',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'details');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a Details
     *
     * @return void
     */
    public function testDetailsGet()
    {
        // Create a fake File for relationships
        $non_linear = factory(NonLinears::class)->create();

        // Set some variables to be checked
        $content = '阳光灿烂的été';

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/details',
            [
                'data' => [
                    'type' => 'details',
                    'attributes' => [
                        'title' => $content,
                    ],
                    'relationships' => [
                        'non_linears' => [
                            'data' => [
                                'type' => 'non_linears',
                                'id' => $non_linear->id,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Creation status
        $response->assertStatus(201);

        // Find created detail
        $detail = Details::orderBy('id', 'DESC')->first();

        // This should return a Details with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/details/'.$detail->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Read status
        $response->assertStatus(200);

        // Check the whole structure
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'created_by',
                    'updated_by',
                    'deleted_by',
                    'title'
                ],
                'relationships' => [
                    'non_linears' => [
                        'data' => [
                            'type',
                            'id',
                        ],
                        'meta' => [
                            'total',
                            'editable',
                        ],
                        'links' => [
                            'self',
                            'related',
                        ],
                    ],
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'details',
                'attributes' => [
                    'title' => $content,
                ],
                'relationships' => [
                    'non_linears' => [
                        'data' => [
                            'type' => 'non_linears',
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ]
                    ],
                ],
            ],
        ]);

        // Check visible relationships ('key' is the relationship name, 'value' is 'many' or 'one'(default))
        $relationships = [
            'non_linears' => 'one',
        ];

        $this->commonRelationshipsCheck('details', $detail->id, $relationships);
    }

    /**
     * Test to update a Details
     *
     * @return void
     */
    public function testDetailsPatch()
    {
        // Create a Details that we will patch
        $non_linear = factory(NonLinears::class)->create();

        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/details',
            [
                'data' => [
                    'type' => 'details',
                    'attributes' => [
                        'title' => $this->faker->text,
                    ],
                    'relationships' => [
                        'non_linears' => [
                            'data' => [
                                'type' => 'non_linears',
                                'id' => $non_linear->id,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Creation status
        $response->assertStatus(201);

        // Fake a new user to find
        $detail = Details::orderBy('id', 'DESC')->first();

        // This should fail and call LinckoJson::error
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/details/'.$detail->id,
            [
                'data' => [
                    'type' => 'details',
                    'id' => $detail->id,
                    'attributes' => [
                        'title' => $this->faker->text,
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Success status
        $response->assertStatus(200);
    }

    /**
     * Test to delete a Details
     *
     * @return void
     */
    public function testDetailsDelete()
    {
        // Fake a Detail
        $detail = factory(Details::class)->create();

        // Soft delete a Detail
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/details/'.$detail->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check Deletion status
        $response->assertStatus(204);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/details/'.$detail->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(404);

        // Check if the json contains some value
        $response->assertJson([
            'message' => 'Resource details with id '.$detail->id.' does not exist.',
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/details?filter[with-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'type' => 'details',
                    'id' => $detail->id,
                    'attributes' => [
                        'title' => $detail->title,
                    ],
                ],
            ],
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/details?filter[only-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'type' => 'details',
                    'id' => $detail->id,
                    'attributes' => [
                        'title' => $detail->title,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a Details by force
     *
     * @return void
     */
    public function testDetailsForceDelete()
    {
        // Create a Details that we will patch
        $detail = factory(Details::class)->create();

        // Fail a forced deletion (Details uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/details/'.$detail->id.'/force_delete',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Item deleted
        $response->assertStatus(204);

        // This should fail because the item is hard deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/details?filter[with-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // The number of Details should be 0
        $count = count($response->json()['data']);
        $this->assertEquals($count, 0);
    }

    /**
     * Test to restore a Details
     *
     * @return void
     */
    public function testDetailsRestore()
    {
        // Fake a Details
        $detail = factory(Details::class)->create();

        // Fail a deletion (Details uses hard deletion)
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/details/'.$detail->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check Deletion status
        $response->assertStatus(204);

        // Fail a forced deletion (Details uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/details/'.$detail->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Read status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'details',
                'id' =>  $detail->id,
                'attributes' => [
                    'title' => $detail->title,
                ],
            ],
        ]);
    }

    /**
     * Test included relationships
     *
     * @return void
     */
    public function testDetailsIncluded()
    {
        // Create a fake File for relationships
        $non_linear = factory(NonLinears::class)->create();

        // Set some variables to be checked
        $content = '阳光灿烂的été';

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/details',
            [
                'data' => [
                    'type' => 'details',
                    'attributes' => [
                        'title' => $content,
                    ],
                    'relationships' => [
                        'non_linears' => [
                            'data' => [
                                'type' => 'non_linears',
                                'id' => $non_linear->id,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Creation status
        $response->assertStatus(201);

        // Find created detail
        $detail = Details::orderBy('id', 'DESC')->first();

        // This should return a Details with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/details/'.$detail->id.'?include=non_linears',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'details');

        // Check Read status
        $response->assertStatus(200);

        // Check the whole structure
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes'
            ],
            'included' => [
                [
                    'type',
                    'id',
                    'attributes'
                ],
            ]
        ]);

        // Check some values
        $response->assertJson([
            'included' => [
                [
                    'type' => 'non_linears',
                    'id' => $non_linear->id,
                    'attributes' => [
                        'link' => $non_linear->link,
                    ],
                ],
            ],
        ]);
    }
}
