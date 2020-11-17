<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Details;

class ProjectsFeatureTest extends TestCase
{
    /**
     * Test to create a Projects
     *
     * @return void
     */
    public function testProjectsPost()
    {
        // Set some variables to be checked
        $title = 'Léo 马丁';

        // Success
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/projects',
            [
                'data' => [
                    'type' => 'projects',
                    'attributes' => [
                        'title' => $title,
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
        $this->commonSingleVndCheck($response, 'projects');

        // Check Creation status
        $response->assertStatus(201);

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
                    'title',
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'projects',
                'attributes' => [
                    'title' => $title,
                ],
            ],
        ]);
    }

    /**
     * Test to fail creating a Projects
     *
     * @return void
     */
    public function testProjectsPostFail()
    {
        // Set some variables to be checked
        $title = 'Léo 马丁';

        // Fail (title required)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/projects',
            [
                'data' => [
                    'type' => 'projects',
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

        // Create a fake Details for relationships
        $detail = factory(Details::class)->create();

        // Fail (unauthorized relation)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/projects',
            [
                'data' => [
                    'type' => 'projects',
                    'attributes' => [
                        'title' => $title,
                    ],
                    'relationships' => [
                        'details' => [
                            'data' => [
                                'type' => 'details',
                                'id' => $detail->id,
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
     * Test to get multiple projects
     *
     * @return void
     */
    public function testProjectsAllGet()
    {
        $count_ori = Projects::count();
        // Then create 2 more fake items
        factory(Projects::class)->create();
        factory(Projects::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/projects',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );
        
        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'projects');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a Projects
     *
     * @return void
     */
    public function testProjectsGet()
    {
        // Set some variables to be checked
        $title = 'Léo 马丁';

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/projects',
            [
                'data' => [
                    'type' => 'projects',
                    'attributes' => [
                        'title' => $title,
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
        $this->commonSingleVndCheck($response, 'projects');

        // Check Creation status
        $response->assertStatus(201);

        // Find created project
        $project = Projects::orderBy('id', 'DESC')->first();

        // We use POST to make sure we get a database ID
        // Create Files children
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/tasks',
            [
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'title' => 'My task',
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

        // Check Creation status
        $response->assertStatus(201);

        // Create Comments children
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => 'My comment'
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

        // Check Creation status
        $response->assertStatus(201);

        // Create NonLinars children
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/non_linears',
            [
                'data' => [
                    'type' => 'non_linears',
                    'attributes' => [
                        'link' => 'https://url.com',
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

        // Check Creation status
        $response->assertStatus(201);

        // This should return a Projects with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/projects/'.$project->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'projects');

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
                    'title',
                ],
                'relationships' => [
                    'children_tasks' => [
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
                    'children_comments' => [
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
                    'children_non_linears' => [
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
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'projects',
                'attributes' => [
                    'title' => $title,
                ],
                'relationships' => [
                    'children_tasks' => [
                        'data' => [
                            [
                                'type' => 'tasks',
                            ],
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => false,
                        ]
                    ],
                    'children_comments' => [
                        'data' => [
                            [
                                'type' => 'comments',
                            ],
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => false,
                        ]
                    ],
                    'children_non_linears' => [
                        'data' => [
                            [
                                'type' => 'non_linears',
                            ],
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => false,
                        ]
                    ],
                ],
            ],
        ]);

        // Check visible relationships ('key' is the relationship name, 'value' is 'many' or 'one'(default))
        $relationships = [
            'children_tasks' => 'many',
            'children_comments' => 'many',
            'children_non_linears' => 'many',
        ];

        foreach ($relationships as $relationship => $type) {
            $response = $this->json(
                'GET',
                '/brunoocto/sample/pm/projects/'.$project->id.'/relationships/'.$relationship,
                [],
                [
                    'Content-Type' => 'application/vnd.api+json',
                    'Accept' => 'application/vnd.api+json',
                    'User-Id' => $this->auth_user->id,
                ]
            );

            // Check Read status
            $response->assertStatus(200);

            // Check the whole structure
            if ($type == 'many') {
                $response->assertJsonStructure([
                    'data' => [
                        [
                            'type',
                            'id',
                        ],
                    ],
                ]);
            } else {
                $response->assertJsonStructure([
                    'data' => [
                        'type',
                        'id',
                    ],
                ]);
            }

            $response = $this->json(
                'GET',
                '/brunoocto/sample/pm/projects/'.$project->id.'/'.$relationship,
                [],
                [
                    'Content-Type' => 'application/vnd.api+json',
                    'Accept' => 'application/vnd.api+json',
                    'User-Id' => $this->auth_user->id,
                ]
            );

            // Check Read status
            $response->assertStatus(200);

            // Check the whole structure
            if ($type == 'many') {
                $response->assertJsonStructure([
                    'data' => [
                        [
                            'type',
                            'id',
                            'attributes',
                        ],
                    ],
                ]);
            } else {
                $response->assertJsonStructure([
                    'data' => [
                        'type',
                        'id',
                        'attributes',
                    ],
                ]);
            }
        }
    }

    /**
     * Test to update a Projects
     *
     * @return void
     */
    public function testProjectsPatch()
    {
        // Set some variables to be checked
        $title = 'Léo 马丁';

        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/projects',
            [
                'data' => [
                    'type' => 'projects',
                    'attributes' => [
                        'title' => $title,
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
        $this->commonSingleVndCheck($response, 'projects');

        // Check Creation status
        $response->assertStatus(201);

        // Fake a new user to find
        $project = Projects::orderBy('id', 'DESC')->first();

        // This should fail and call LinckoJson::error
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/projects/'.$project->id,
            [
                'data' => [
                    'type' => 'projects',
                    'id' => $project->id,
                    'attributes' => [
                        'title' => 'new title',
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
        $this->commonSingleVndCheck($response, 'projects');

        // Check Success status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'projects',
                'attributes' => [
                    'title' => 'new title',
                ],
            ],
        ]);
    }

    /**
     * Test to delete a Projects
     *
     * @return void
     */
    public function testProjectsDelete()
    {
        // Fake a Project
        $project = factory(Projects::class)->create();

        // Soft delete a Project
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/projects/'.$project->id,
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
            '/brunoocto/sample/pm/projects/'.$project->id,
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
            'message' => 'Resource projects with id '.$project->id.' does not exist.',
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/projects?filter[with-trashed]=true',
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
                    'type' => 'projects',
                    'id' => $project->id,
                    'attributes' => [
                        'title' => $project->title,
                    ],
                ],
            ],
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/projects?filter[only-trashed]=true',
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
                    'type' => 'projects',
                    'id' => $project->id,
                    'attributes' => [
                        'title' => $project->title,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a Projects by force
     *
     * @return void
     */
    public function testProjectsForceDelete()
    {
        // Create a Projects that we will patch
        $project = factory(Projects::class)->create();

        // Fail a forced deletion (Projects uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/projects/'.$project->id.'/force_delete',
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
            '/brunoocto/sample/pm/projects?filter[with-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // The number of Projects should be 0
        $count = count($response->json()['data']);
        $this->assertEquals($count, 0);
    }

    /**
     * Test to restore a Projects
     *
     * @return void
     */
    public function testProjectsRestore()
    {
        // Fake a Projects
        $project = factory(Projects::class)->create();

        // Fail a deletion (Projects uses hard deletion)
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/projects/'.$project->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check Deletion status
        $response->assertStatus(204);

        // Fail a forced deletion (Projects uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/projects/'.$project->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'projects');

        // Check Read status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'projects',
                'id' =>  $project->id,
                'attributes' => [
                    'title' => $project->title,
                ],
            ],
        ]);
    }
}
