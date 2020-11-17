<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\Files;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;
use Brunoocto\Sample\Models\ProjectManagement\Comments;
use Brunoocto\Sample\Models\ProjectManagement\NonLinears;
use Brunoocto\Sample\Models\ProjectManagement\Milestones;

class FilesFeatureTest extends TestCase
{
    /**
     * Test to create a Files
     *
     * @return void
     */
    public function testFilesPost()
    {
        // Create fakes for relationships
        $project_1 = factory(Projects::class)->create();
        $project_2 = factory(Projects::class)->create();
        $task_1 = factory(Tasks::class)->create();
        $task_2 = factory(Tasks::class)->create();
        $comment_1 = factory(Comments::class)->create();
        $comment_2 = factory(Comments::class)->create();
        $non_linear_1 = factory(NonLinears::class)->create();
        $non_linear_2 = factory(NonLinears::class)->create();

        // Success
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
                        'projects' => [
                            'data' => [
                                [
                                    'type' => 'projects',
                                    'id' => $project_1->id,
                                    'visibility' => 1,
                                ],
                                [
                                    'type' => 'projects',
                                    'id' => $project_2->id,
                                ]
                            ],
                        ],
                        'tasks' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task_1->id,
                                    'visibility' => 0,
                                ],
                                [
                                    'type' => 'tasks',
                                    'id' => $task_2->id,
                                ]
                            ],
                        ],
                        'comments' => [
                            'data' => [
                                [
                                    'type' => 'comments',
                                    'id' => $comment_1->id,
                                ],
                                [
                                    'type' => 'comments',
                                    'id' => $comment_2->id,
                                ]
                            ],
                        ],
                        'non_linears' => [
                            'data' => [
                                [
                                    'type' => 'non_linears',
                                    'id' => $non_linear_1->id,
                                ],
                                [
                                    'type' => 'non_linears',
                                    'id' => $non_linear_2->id,
                                ]
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
        $this->commonSingleVndCheck($response, 'files');

        // Check Creation status
        $response->assertStatus(201);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'files',
                'attributes' => [
                    'title' => 'My video',
                    'mime' => 'video/mp4',
                    'path' => '/tmp',
                    'bytes' => 720,
                ],
                'relationships' => [
                    'projects' => [
                        'data' => [
                            [
                                'type' => 'projects',
                                'id' => $project_1->id,
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                            [
                                'type' => 'projects',
                                'id' => $project_2->id,
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 2,
                            'editable' => true,
                        ],
                    ],
                    'tasks' => [
                        'data' => [
                            [
                                'type' => 'tasks',
                                'id' => $task_1->id,
                                'meta' => [
                                    'visibility' => '0',
                                ],
                            ],
                            [
                                'type' => 'tasks',
                                'id' => $task_2->id,
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 2,
                            'editable' => true,
                        ],
                    ],
                    'comments' => [
                        'data' => [
                            [
                                'type' => 'comments',
                                'id' => $comment_1->id,
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                            [
                                'type' => 'comments',
                                'id' => $comment_2->id,
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 2,
                            'editable' => true,
                        ],
                    ],
                    'non_linears' => [
                        'data' => [
                            [
                                'type' => 'non_linears',
                                'id' => $non_linear_1->id,
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                            [
                                'type' => 'non_linears',
                                'id' => $non_linear_2->id,
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 2,
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
                    'title'
                ],
                'relationships' => [
                    'projects' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
                                ],
                            ],
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
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
                    'tasks' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
                                ],
                            ],
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
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
                    'comments' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
                                ],
                            ],
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
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
                    'non_linears' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
                                ],
                            ],
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'visibility'
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
    }

    /**
     * Test to fail creating a Files
     *
     * @return void
     */
    public function testFilesPostFail()
    {
        // Create a fake File for relationships
        $milestone = factory(Milestones::class)->create();

        // Fail (missing relationships)
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
                        'milestones' => [
                            'data' => [
                                [
                                    'type' => 'milestones',
                                    'id' => $milestone->id,
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
     * Test to get multiple files
     *
     * @return void
     */
    public function testFilesAllGet()
    {
        $count_ori = Files::count();
        // Then create 2 more fake items
        factory(Files::class)->create();
        factory(Files::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/files',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'files');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a Files
     *
     * @return void
     */
    public function testFilesGet()
    {
        // Create fake relationships
        $project = factory(Projects::class)->create();
        $task = factory(Tasks::class)->create();
        $comment = factory(Comments::class)->create();
        $non_linear = factory(NonLinears::class)->create();

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
                        'projects' => [
                            'data' => [
                                [
                                    'type' => 'projects',
                                    'id' => $project->id,
                                    'visibility' => 0,
                                ],
                            ],
                        ],
                        'tasks' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task->id,
                                    'visibility' => 1,
                                ],
                            ],
                        ],
                        'comments' => [
                            'data' => [
                                [
                                    'type' => 'comments',
                                    'id' => $comment->id,
                                ],
                            ],
                        ],
                        'non_linears' => [
                            'data' => [
                                [
                                    'type' => 'non_linears',
                                    'id' => $non_linear->id,
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

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'files');

        // Check Creation status
        $response->assertStatus(201);

        // Find created file
        $file = Files::orderBy('id', 'DESC')->first();

        // Create a parent
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => 'test',
                    ],
                    'relationships' => [
                        'files' => [
                            'data' => [
                                'type' => 'files',
                                'id' => $file->id,
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

        // This should return a Files with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/files/'.$file->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'files');

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
                    'projects' => [
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
                    'tasks' => [
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
                    'comments' => [
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
                    'non_linears' => [
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
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'files',
                'attributes' => [
                    'title' => 'My video',
                    'mime' => 'video/mp4',
                    'path' => '/tmp',
                    'bytes' => 720,
                ],
                'relationships' => [
                    'projects' => [
                        'data' => [
                            [
                                'type' => 'projects',
                                'meta' => [
                                    'visibility' => '0',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ]
                    ],
                    'tasks' => [
                        'data' => [
                            [
                                'type' => 'tasks',
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ]
                    ],
                    'comments' => [
                        'data' => [
                            [
                                'type' => 'comments',
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ]
                    ],
                    'non_linears' => [
                        'data' => [
                            [
                                'type' => 'non_linears',
                                'meta' => [
                                    'visibility' => '1',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
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
                ],
            ],
        ]);

        // Check visible relationships ('key' is the relationship name, 'value' is 'many' or 'one'(default))
        $relationships = [
            'projects' => 'many',
            'tasks' => 'many',
            'comments' => 'many',
            'non_linears' => 'many',
            'children_comments' => 'many',
        ];

        $this->commonRelationshipsCheck('files', $file->id, $relationships);
    }

    /**
     * Test to update a Files
     *
     * @return void
     */
    public function testFilesPatch()
    {
        // Create a Files that we will patch
        $non_linear = factory(NonLinears::class)->create();

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

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'files');

        // Check Creation status
        $response->assertStatus(201);

        // Fake a new user to find
        $file = Files::orderBy('id', 'DESC')->first();

        // This should fail and call LinckoJson::error
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/files/'.$file->id,
            [
                'data' => [
                    'type' => 'files',
                    'id' => $file->id,
                    'attributes' => [
                        'bytes' => 800,
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
        $this->commonSingleVndCheck($response, 'files');

        // Check Success status
        $response->assertStatus(200);
    }

    /**
     * Test to delete a Files
     *
     * @return void
     */
    public function testFilesDelete()
    {
        // Fake a File
        $file = factory(Files::class)->create();

        // Soft delete a File
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/files/'.$file->id,
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
            '/brunoocto/sample/pm/files/'.$file->id,
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
            'message' => 'Resource files with id '.$file->id.' does not exist.',
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/files?filter[with-trashed]=true',
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
                    'type' => 'files',
                    'id' => $file->id,
                    'attributes' => [
                        'title' => $file->title,
                    ],
                ],
            ],
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/files?filter[only-trashed]=true',
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
                    'type' => 'files',
                    'id' => $file->id,
                    'attributes' => [
                        'title' => $file->title,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a Files by force
     *
     * @return void
     */
    public function testFilesForceDelete()
    {
        // Create a Files that we will patch
        $file = factory(Files::class)->create();

        // Fail a forced deletion (Files uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/files/'.$file->id.'/force_delete',
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
            '/brunoocto/sample/pm/files?filter[with-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // The number of Files should be 0
        $count = count($response->json()['data']);
        $this->assertEquals($count, 0);
    }

    /**
     * Test to restore a Files
     *
     * @return void
     */
    public function testFilesRestore()
    {
        // Fake a Files
        $file = factory(Files::class)->create();

        // Fail a deletion (Files uses hard deletion)
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/files/'.$file->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check Deletion status
        $response->assertStatus(204);

        // Fail a forced deletion (Files uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/files/'.$file->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'files');

        // Check Read status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'files',
                'id' =>  $file->id,
                'attributes' => [
                    'title' => $file->title,
                ],
            ],
        ]);
    }

    /**
     * Test filtering
     *
     * @return void
     */
    public function testFilesFilter()
    {
        // Create items
        $file_1 = factory(Files::class)->create([
            'title' => 'bruno',
        ]);
        $file_2 = factory(Files::class)->create([
            'title' => 'Martin',
        ]);
        $file_3 = factory(Files::class)->create([
            'title' => 'bruno',
        ]);

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/files?filter[title]=Martin',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'files');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count = count($response->json()['data']);
        $this->assertEquals($count, 1);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'id' =>  $file_2->id,
                ],
            ],
        ]);
    }

    /**
     * Test included relationships
     *
     * @return void
     */
    public function testFilesIncluded()
    {
        // Create fake relationships
        $project = factory(Projects::class)->create();
        $task = factory(Tasks::class)->create();
        $comment = factory(Comments::class)->create();
        $non_linear = factory(NonLinears::class)->create();

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
                        'projects' => [
                            'data' => [
                                [
                                    'type' => 'projects',
                                    'id' => $project->id,
                                    'visibility' => 0,
                                ],
                            ],
                        ],
                        'tasks' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task->id,
                                    'visibility' => 1,
                                ],
                            ],
                        ],
                        'comments' => [
                            'data' => [
                                [
                                    'type' => 'comments',
                                    'id' => $comment->id,
                                ],
                            ],
                        ],
                        'non_linears' => [
                            'data' => [
                                [
                                    'type' => 'non_linears',
                                    'id' => $non_linear->id,
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

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'files');

        // Check Creation status
        $response->assertStatus(201);

        // Find created file
        $file = Files::orderBy('id', 'DESC')->first();

        // This should return a Files with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/files/'.$file->id.'?include=projects,tasks,comments,non_linears',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'files');

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
                [
                    'type',
                    'id',
                    'attributes'
                ],
                [
                    'type',
                    'id',
                    'attributes'
                ],
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
                    'type' => 'projects',
                    'id' => $project->id,
                    'attributes' => [
                        'title' => $project->title,
                    ],
                ],
                [
                    'type' => 'tasks',
                    'id' => $task->id,
                    'attributes' => [
                        'title' => $task->title,
                    ],
                ],
                [
                    'type' => 'comments',
                    'id' => $comment->id,
                    'attributes' => [
                        'content' => $comment->content,
                    ],
                ],
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
