<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;
use Brunoocto\Sample\Models\ProjectManagement\Details;

class TasksFeatureTest extends TestCase
{
    /**
     * Test to create a Tasks
     *
     * @return void
     */
    public function testTasksPost()
    {
        // Create fakes for relationships
        $project = factory(Projects::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';

        // Success
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/tasks',
            [
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'title' => $title,
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
        $this->commonSingleVndCheck($response, 'tasks');

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
                    'content',
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

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'tasks',
                'attributes' => [
                    'title' => $title,
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
    }

    /**
     * Test to fail creating a Tasks
     *
     * @return void
     */
    public function testTasksPostFail()
    {
        // Create a fake Projects for relationships
        $project = factory(Projects::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';

        // Fail (relationship required)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/tasks',
            [
                'data' => [
                    'type' => 'tasks',
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

        // Check that an expection is sent
        $this->assertTrue(isset($response->exception));

        // Fail (unauthorized relation)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/tasks',
            [
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'title' => $title,
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

        // Create a fake Details for relationships
        $detail = factory(Details::class)->create();

        // Fail (unauthorized relation)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/tasks',
            [
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'title' => $title,
                    ],
                    'relationships' => [
                        'details' => [
                            'data' => [
                                [
                                    'type' => 'details',
                                    'id' => $detail->id,
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
     * Test to get multiple tasks
     *
     * @return void
     */
    public function testTasksAllGet()
    {
        $count_ori = Tasks::count();
        // Then create 2 more fake items
        factory(Tasks::class)->create();
        factory(Tasks::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/tasks',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'tasks');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a Tasks
     *
     * @return void
     */
    public function testTasksGet()
    {
        // Create fake relationships
        $project = factory(Projects::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/tasks',
            [
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'title' => $title,
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
        $this->commonSingleVndCheck($response, 'tasks');

        // Check Creation status
        $response->assertStatus(201);

        // Find created task
        $task = Tasks::orderBy('id', 'DESC')->first();

        // We use POST to make sure we get a database ID
        // Create Files children
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
                        'tasks' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task->id,
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
                        'content' => 'test'
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

        // Check Creation status
        $response->assertStatus(201);

        // Create Milestones children
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => 'test',
                        'deadline' => 1595375203,
                    ],
                    'relationships' => [
                        'jobs' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task->id,
                                    'priority' => 4,
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

        // Check Creation status
        $response->assertStatus(201);

        // This should return a Tasks with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/tasks/'.$task->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'tasks');

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
                    'content',
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
                    'children_files' => [
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
                    'children_milestones' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'priority',
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
                'type' => 'tasks',
                'attributes' => [
                    'title' => $title,
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
                    'children_files' => [
                        'data' => [
                            [
                                'type' => 'files',
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
                    'children_milestones' => [
                        'data' => [
                            [
                                'type' => 'milestones',
                                'meta' => [
                                    'priority' => '4',
                                ],
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
            'projects' => 'one',
            'children_files' => 'many',
            'children_comments' => 'many',
            'children_milestones' => 'many',
        ];

        $this->commonRelationshipsCheck('tasks', $task->id, $relationships);
    }

    /**
     * Test to update a Tasks
     *
     * @return void
     */
    public function testTasksPatch()
    {
        // Create fake relationships
        $project_1 = factory(Projects::class)->create();
        $project_2 = factory(Projects::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';
        $content = '阳光灿烂的été';

        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/tasks',
            [
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'title' => $title,
                        'content' => $content,
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
        $this->commonSingleVndCheck($response, 'tasks');

        // Check Creation status
        $response->assertStatus(201);

        // Fake a new user to find
        $task = Tasks::orderBy('id', 'DESC')->first();

        // This should fail and call LinckoJson::error
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/tasks/'.$task->id,
            [
                'data' => [
                    'type' => 'tasks',
                    'id' => $task->id,
                    'attributes' => [
                        'title' => 'new title',
                        'content' => 'new content',
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
        $this->commonSingleVndCheck($response, 'tasks');

        // Check Success status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'tasks',
                'attributes' => [
                    'title' => 'new title',
                    'content' => 'new content',
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
     * Test to delete a Tasks
     *
     * @return void
     */
    public function testTasksDelete()
    {
        // Fake a Task
        $task = factory(Tasks::class)->create();

        // Soft delete a Task
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/tasks/'.$task->id,
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
            '/brunoocto/sample/pm/tasks/'.$task->id,
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
            'message' => 'Resource tasks with id '.$task->id.' does not exist.',
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/tasks?filter[with-trashed]=true',
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
                    'type' => 'tasks',
                    'id' => $task->id,
                    'attributes' => [
                        'title' => $task->title,
                    ],
                ],
            ],
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/tasks?filter[only-trashed]=true',
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
                    'type' => 'tasks',
                    'id' => $task->id,
                    'attributes' => [
                        'title' => $task->title,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a Tasks by force
     *
     * @return void
     */
    public function testTasksForceDelete()
    {
        // Create a Tasks that we will patch
        $task = factory(Tasks::class)->create();

        // Fail a forced deletion (Tasks uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/tasks/'.$task->id.'/force_delete',
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
            '/brunoocto/sample/pm/tasks?filter[with-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // The number of Tasks should be 0
        $count = count($response->json()['data']);
        $this->assertEquals($count, 0);
    }

    /**
     * Test to restore a Tasks
     *
     * @return void
     */
    public function testTasksRestore()
    {
        // Fake a Tasks
        $task = factory(Tasks::class)->create();

        // Fail a deletion (Tasks uses hard deletion)
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/tasks/'.$task->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check Deletion status
        $response->assertStatus(204);

        // Fail a forced deletion (Tasks uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/tasks/'.$task->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'tasks');

        // Check Read status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'tasks',
                'id' =>  $task->id,
                'attributes' => [
                    'title' => $task->title,
                ],
            ],
        ]);
    }
}
