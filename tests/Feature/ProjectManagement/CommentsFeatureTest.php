<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\Comments;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;
use Brunoocto\Sample\Models\ProjectManagement\Files;
use Brunoocto\Sample\Models\ProjectManagement\Milestones;

class CommentsFeatureTest extends TestCase
{
    /**
     * Test to create a comment
     *
     * @return void
     */
    public function testCommentsPost()
    {
        // Create a fake File for relationships
        $file = factory(Files::class)->create();

        // Set some variables to be checked
        $content = 'Léo 马丁';

        // Success
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => $content,
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

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'comments');

        // Check Creation status
        $response->assertStatus(201);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'comments',
                'attributes' => [
                    'content' => $content,
                ],
                'relationships' => [
                    'files' => [
                        'data' => [
                            'type' => 'files',
                            'id' => $file->id,
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
                    'created_by',
                    'content'
                ],
                'relationships' => [
                    'files' => [
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

        // Test all other relations
        $entities = [
            'projects' => factory(Projects::class)->create(),
            'tasks' => factory(Tasks::class)->create(),
            'comments' => factory(Comments::class)->create(),
        ];

        foreach ($entities as $type => $entity) {
            // Success
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
                        $type => [
                            'data' => [
                                'type' => $type,
                                'id' => $entity->id,
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
            $this->commonSingleVndCheck($response, 'comments');

            // Check Creation status
            $response->assertStatus(201);
        }
    }

    /**
     * Test to fail creating a comment
     *
     * @return void
     */
    public function testCommentsPostFail()
    {
        // Create a fake File for relationships
        $file = factory(Files::class)->create();

        // Set some variables to be checked
        $content = 'Léo 马丁';

        // Fail (missing relationships)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => $content,
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

        // Fail (wrong relation: toMany)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => $content,
                    ],
                    'relationships' => [
                        'files' => [
                            'data' => [
                                [
                                    'type' => 'files',
                                    'id' => $file->id,
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

        // Fail (unauthorized relation)
        // Create a fake File for relationships
        $milestone = factory(Milestones::class)->create();
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => $content,
                    ],
                    'relationships' => [
                        'milestones' => [
                            'data' => [
                                'type' => 'milestones',
                                'id' => $milestone->id,
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
     * Test to get multiple comments
     *
     * @return void
     */
    public function testCommentsAllGet()
    {
        $count_ori = Comments::count();
        // Then create 2 more fake items
        factory(Comments::class)->create();
        factory(Comments::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/comments',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );
        
        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'comments');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a comment
     *
     * @return void
     */
    public function testCommentsGet()
    {
        // Create a fake File for relationships
        $file = factory(Files::class)->create();

        // Set some variables to be checked
        $content = '阳光灿烂的été';

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => $content,
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

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'comments');

        // Check Creation status
        $response->assertStatus(201);

        // Find created comment
        $comment = Comments::orderBy('id', 'DESC')->first();

        // Create a 2 Children comments
        for ($i=0; $i<2; $i++) {
            $response = $this->json(
                'POST',
                '/brunoocto/sample/pm/comments',
                [
                    'data' => [
                        'type' => 'comments',
                        'attributes' => [
                            'content' => $content,
                        ],
                        'relationships' => [
                            'comments' => [
                                'data' => [
                                    'type' => 'comments',
                                    'id' => $comment->id,
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
        }

        // Create a Children file
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
                        'comments' => [
                            'data' => [
                                [
                                    'type' => 'comments',
                                    'id' => $comment->id,
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

        // This should return a comment with 1 Files as parent, 1 Files and 2 Comments as children
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/comments/'.$comment->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'comments');

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
                    'created_by',
                    'content'
                ],
                'relationships' => [
                    'files' => [
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
                    'children_comments' => [
                        'data' => [
                            [
                                'type',
                                'id',
                            ],
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
                    'children_files' => [
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
                'type' => 'comments',
                'attributes' => [
                    'content' => $content,
                ],
                'relationships' => [
                    'files' => [
                        'data' => [
                            'type' => 'files',
                            'id' => $file->id,
                        ],
                        'meta' => [
                            'total' => 1,
                            'editable' => true,
                        ]
                    ],
                    'children_comments' => [
                        'data' => [
                            // We cannot check ID in many relationships because we cannot confirm which ID will come first
                            [
                                'type' => 'comments',
                            ],
                            [
                                'type' => 'comments',
                            ],
                        ],
                        'meta' => [
                            'total' => 2,
                            'editable' => false,
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
                ],
            ],
        ]);

        // Check visible relationships ('key' is the relationship name, 'value' is 'many' or 'one'(default))
        // Other relations are not tested because it is a relation at a time
        $relationships = [
            'files' => 'one',
            'children_comments' => 'many',
            'children_files' => 'many',
        ];

        $this->commonRelationshipsCheck('comments', $comment->id, $relationships);

        // Other relations should return empty data
        $relationships = [
            'projects' => 'one',
            'tasks' => 'one',
            'comments' => 'one',
        ];

        foreach ($relationships as $relationship => $relation) {
            $response = $this->json(
                'GET',
                '/brunoocto/sample/pm/comments/'.$comment->id.'/relationships/'.$relationship,
                [],
                [
                    'Content-Type' => 'application/vnd.api+json',
                    'Accept' => 'application/vnd.api+json',
                    'User-Id' => $this->auth_user->id,
                ]
            );

            // Check Read status
            $response->assertStatus(200);

            // Check that at list data is present
            $response->assertJsonStructure([
                'data'
            ]);

            $response = $this->json(
                'GET',
                '/brunoocto/sample/pm/comments/'.$comment->id.'/'.$relationship,
                [],
                [
                    'Content-Type' => 'application/vnd.api+json',
                    'Accept' => 'application/vnd.api+json',
                    'User-Id' => $this->auth_user->id,
                ]
            );

            // Check Read status
            $response->assertStatus(200);

            // Check that at list data is present
            $response->assertJsonStructure([
                'data'
            ]);
        }
    }

    /**
     * Test to update a comment
     *
     * @return void
     */
    public function testCommentsPatchFail()
    {
        // Create a comment that we will patch
        $comment = factory(Comments::class)->create();

        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/comments',
            [
                'data' => [
                    'type' => 'comments',
                    'attributes' => [
                        'content' => $this->faker->text,
                    ],
                    'relationships' => [
                        'comments' => [
                            'data' => [
                                'type' => 'comments',
                                'id' => $comment->id,
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
        $this->commonSingleVndCheck($response, 'comments');

        // Check Creation status
        $response->assertStatus(201);

        // Find last created Comments
        $comment = Comments::orderBy('id', 'DESC')->first();

        // This should fail and call LinckoJson::error
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/comments/'.$comment->id,
            [
                'data' => [
                    'type' => 'comments',
                    'id' => $comment->id,
                    'attributes' => [
                        'content' => $this->faker->text,
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
     * Test to delete a comment
     *
     * @return void
     */
    public function testCommentsDeleteFail()
    {
        // Fake a comment
        $comment = factory(Comments::class)->create();

        // Fail a deletion (Comments uses hard deletion)
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/comments/'.$comment->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check that an expection is sent
        $this->assertTrue(isset($response->exception));

        // Fake a comment
        $comment = factory(Comments::class)->create();
        // The next call will use LinckoJson::error which does throw an exception (mockery)
        $this->expectException('Exception');
        $comment->delete();
        // NOTE: Because of the exception, no following code will be run, teh test will end here
    }

    /**
     * Test to delete a comment by force
     *
     * @return void
     */
    public function testCommentsForceDeleteFail()
    {
        // Create a comment that we will patch
        $comment = factory(Comments::class)->create();

        // Fail a forced deletion (Comments uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/comments/'.$comment->id.'/force_delete',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // The route should not exists
        $response->assertStatus(404);

        // Fake a comment
        $comment = factory(Comments::class)->create();
        // The next call will use LinckoJson::error which does throw an exception (mockery)
        $this->expectException('Exception');
        $comment->forceDelete();
        // NOTE: Because of the exception, no following code will be run, teh test will end here
    }

    /**
     * Test to restore a comment
     *
     * @return void
     */
    public function testCommentsRestoreFail()
    {
        // Create a comment that we will patch
        $comment = factory(Comments::class)->create();

        // Fail a forced deletion (Comments uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/comments/'.$comment->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // The route should not exists
        $response->assertStatus(404);

        // Fake a comment
        $comment = factory(Comments::class)->create();
        // The next call will use LinckoJson::error which does throw an exception (mockery)
        $this->expectException('Exception');
        $comment->restoreItem();
        // NOTE: Because of the exception, no following code will be run, teh test will end here
    }
}
