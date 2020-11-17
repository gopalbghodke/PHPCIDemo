<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\NonLinears;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;

class NonLinearsFeatureTest extends TestCase
{
    /**
     * Test to create a NonLinears
     *
     * @return void
     */
    public function testNonLinearsPost()
    {
        // Create fakes for relationships
        $project = factory(Projects::class)->create();

        // Set some variables to be checked
        $url = $this->faker->url;

        // Success
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/non_linears',
            [
                'data' => [
                    'type' => 'non_linears',
                    'attributes' => [
                        'link' => $url,
                    ],
                    'relationships' => [
                        'projects' => [
                            'data' => [
                                'type' => 'projects',
                                'id' => $project->id,
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
        $this->commonSingleVndCheck($response, 'non_linears');

        // Check Creation status
        $response->assertStatus(201);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'non_linears',
                'attributes' => [
                    'link' => $url,
                ],
                'relationships' => [
                    'projects' => [
                        'data' => [
                            'type' => 'projects',
                            'id' => $project->id,
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ],
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
                    'link',
                ],
                'relationships' => [
                    'projects' => [
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
     * Test to fail creating a NonLinears
     *
     * @return void
     */
    public function testNonLinearsPostFail()
    {
        // Create a fake Projects for relationships
        $task = factory(Tasks::class)->create();

        // Set some variables to be checked
        $url = $this->faker->url;

        // Fail (relation required)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/non_linears',
            [
                'data' => [
                    'type' => 'non_linears',
                    'attributes' => [
                        'link' => $url,
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
            '/brunoocto/sample/pm/non_linears',
            [
                'data' => [
                    'type' => 'non_linears',
                    'attributes' => [
                        'link' => $url,
                    ],
                    'relationships' => [
                        'tasks' => [
                            'data' => [
                                'type' => 'tasks',
                                'id' => $task->id,
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

        // Create a fake Tasks for relationships
        $project = factory(Projects::class)->create();

        // Fail (unauthorized relation)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/non_linears',
            [
                'data' => [
                    'type' => 'non_linears',
                    'attributes' => [
                        'link' => $url,
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
     * Test to get multiple non_linears
     *
     * @return void
     */
    public function testNonLinearsAllGet()
    {
        $count_ori = NonLinears::count();
        // Then create 2 more fake items
        factory(NonLinears::class)->create();
        factory(NonLinears::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/non_linears',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'non_linears');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a NonLinears
     *
     * @return void
     */
    public function testNonLinearsGet()
    {
        // Create fake relationships
        $project = factory(Projects::class)->create();

        // Set some variables to be checked
        $url = $this->faker->url;

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/non_linears',
            [
                'data' => [
                    'type' => 'non_linears',
                    'attributes' => [
                        'link' => $url,
                    ],
                    'relationships' => [
                        'projects' => [
                            'data' => [
                                'type' => 'projects',
                                'id' => $project->id,
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
        $this->commonSingleVndCheck($response, 'non_linears');

        // Check Creation status
        $response->assertStatus(201);

        // Find created non_linear
        $non_linear = NonLinears::orderBy('id', 'DESC')->first();

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/details',
            [
                'data' => [
                    'type' => 'details',
                    'attributes' => [
                        'title' => 'title',
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

        // Check Creation status
        $response->assertStatus(201);

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/files',
            [
                'data' => [
                    'type' => 'files',
                    'attributes' => [
                        'title' => 'My video',
                        'mime' => 'video/mp4',
                        'path' => '/tmp',
                        'bytes' => 720,
                    ],
                    'relationships' => [
                        'non_linears' => [
                            'data' => [
                                [
                                    'type' => 'non_linears',
                                    'id' => $non_linear->id,
                                    'meta' => [
                                        'visibility' => 0,
                                    ],
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

        // This should return a NonLinears with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/non_linears/'.$non_linear->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'non_linears');

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
                    'link',
                ],
                'relationships' => [
                    'projects' => [
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
                    'details' => [
                        'data' => [
                            [
                                'type',
                                'id',
                            ],
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
                    'files' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility',
                                ],
                            ],
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
                'type' => 'non_linears',
                'attributes' => [
                    'link' => $url,
                ],
                'relationships' => [
                    'projects' => [
                        'data' => [
                            'type' => 'projects',
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ]
                    ],
                    'details' => [
                        'data' => [
                            [
                                'type' => 'details',
                            ]
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => false,
                        ]
                    ],
                    'files' => [
                        'data' => [
                            [
                                'type' => 'files',
                                'meta' => [
                                    'visibility' => 0,
                                ],
                            ]
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => false,
                        ]
                    ],
                ],
            ],
        ]);

        // Check visible relationships
        $relationships = [
            'projects' => 'one',
            'details' => 'many',
            'files' => 'many',
        ];

        $this->commonRelationshipsCheck('non_linears', $non_linear->id, $relationships);
    }

    /**
     * Test to update a NonLinears
     *
     * @return void
     */
    public function testNonLinearsPatch()
    {
        // Create fake relationships
        $project_1 = factory(Projects::class)->create();
        $project_2 = factory(Projects::class)->create();

        // Set some variables to be checked
        $url_1 = 'https://url1.com';
        $url_2 = 'https://url2.com';

        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/non_linears',
            [
                'data' => [
                    'type' => 'non_linears',
                    'attributes' => [
                        'link' => $url_1,
                    ],
                    'relationships' => [
                        'projects' => [
                            'data' => [
                                'type' => 'projects',
                                'id' => $project_1->id,
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
        $this->commonSingleVndCheck($response, 'non_linears');

        // Check Creation status
        $response->assertStatus(201);

        // Fake a new user to find
        $non_linear = NonLinears::orderBy('id', 'DESC')->first();

        // This should fail and call LinckoJson::error
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/non_linears/'.$non_linear->id,
            [
                'data' => [
                    'type' => 'non_linears',
                    'id' => $non_linear->id,
                    'attributes' => [
                        'link' => $url_2,
                    ],
                    'relationships' => [
                        'projects' => [
                            'data' => [
                                'type' => 'projects',
                                'id' => $project_2->id,
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
        $this->commonSingleVndCheck($response, 'non_linears');

        // Check Success status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'non_linears',
                'attributes' => [
                    'link' => $url_2,
                ],
                'relationships' => [
                    'projects' => [
                        'data' => [
                            'type' => 'projects',
                            'id' => $project_2->id,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a NonLinears
     *
     * @return void
     */
    public function testNonLinearsDelete()
    {
        // Fake a NonLinear
        $non_linear = factory(NonLinears::class)->create();

        // Soft delete a NonLinear
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/non_linears/'.$non_linear->id,
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
            '/brunoocto/sample/pm/non_linears/'.$non_linear->id,
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
            'message' => 'Resource non_linears with id '.$non_linear->id.' does not exist.',
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/non_linears?filter[with-trashed]=true',
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
                    'type' => 'non_linears',
                    'id' => $non_linear->id,
                    'attributes' => [
                        'link' => $non_linear->link,
                    ],
                ],
            ],
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/non_linears?filter[only-trashed]=true',
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
                    'type' => 'non_linears',
                    'id' => $non_linear->id,
                    'attributes' => [
                        'link' => $non_linear->link,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a NonLinears by force
     *
     * @return void
     */
    public function testNonLinearsForceDelete()
    {
        // Create a NonLinears that we will patch
        $non_linear = factory(NonLinears::class)->create();

        // Fail a forced deletion (NonLinears uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/non_linears/'.$non_linear->id.'/force_delete',
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
            '/brunoocto/sample/pm/non_linears?filter[with-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // The number of NonLinears should be 0
        $count = count($response->json()['data']);
        $this->assertEquals($count, 0);
    }

    /**
     * Test to restore a NonLinears
     *
     * @return void
     */
    public function testNonLinearsRestore()
    {
        // Fake a NonLinears
        $non_linear = factory(NonLinears::class)->create();

        // Fail a deletion (NonLinears uses hard deletion)
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/non_linears/'.$non_linear->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check Deletion status
        $response->assertStatus(204);

        // Fail a forced deletion (NonLinears uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/non_linears/'.$non_linear->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'non_linears');

        // Check Read status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'non_linears',
                'id' =>  $non_linear->id,
                'attributes' => [
                    'link' => $non_linear->link,
                ],
            ],
        ]);
    }
}
