<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\Milestones;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;

class MilestonesFeatureTest extends TestCase
{
    /**
     * Test to create a Milestones
     *
     * @return void
     */
    public function testMilestonesPost()
    {
        // Create fakes for relationships
        $task_1 = factory(Tasks::class)->create();
        $task_2 = factory(Tasks::class)->create();
        $task_3 = factory(Tasks::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';
        $deadline = 1595375203;

        // Relationship is not required
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => $title,
                        'deadline' => $deadline,
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
        $this->commonSingleVndCheck($response, 'milestones');

        // Check Creation status
        $response->assertStatus(201);

        // Success
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => $title,
                        'deadline' => $deadline,
                    ],
                    'relationships' => [
                        'jobs' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task_1->id,
                                ],
                                [
                                    'type' => 'tasks',
                                    'id' => $task_2->id,
                                    'priority' => 2,
                                ],
                                [
                                    'type' => 'tasks',
                                    'id' => $task_3->id,
                                    'meta' => [
                                        'priority' => 3,
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

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'milestones');

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
                    'deadline',
                ],
                'relationships' => [
                    'jobs' => [
                        'data' => [
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'priority'
                                ],
                            ],
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'priority'
                                ],
                            ],
                            [
                                'type',
                                'id',
                                'meta' => [
                                    'priority'
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
                'type' => 'milestones',
                'attributes' => [
                    'title' => $title,
                    'deadline' => $deadline,
                ],
                'relationships' => [
                    'jobs' => [
                        'data' => [
                            [
                                'type' => 'tasks',
                                'id' => $task_1->id,
                            ],
                            [
                                'type' => 'tasks',
                                'id' => $task_2->id,
                                'meta' => [
                                    'priority' => '2',
                                ],
                            ],
                            [
                                'type' => 'tasks',
                                'id' => $task_3->id,
                                'meta' => [
                                    'priority' => '3',
                                ],
                            ],
                        ],
                        'meta' => [
                            'total' => 3,
                            'editable' => true,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to fail creating a Milestones
     *
     * @return void
     */
    public function testMilestonesPostFail()
    {
        // Create a fake Projects for relationships
        $project = factory(Projects::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';
        $deadline = 1595375203;

        // Fail (unauthorized relation)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => $title,
                        'deadline' => $deadline,
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

        // Create a fake Tasks for relationships
        $task = factory(Tasks::class)->create();

        // Fail (unauthorized relation)
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => $title,
                        'deadline' => $deadline,
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

        // Check that an expection is sent
        $this->assertTrue(isset($response->exception));
    }

    /**
     * Test to get multiple milestones
     *
     * @return void
     */
    public function testMilestonesAllGet()
    {
        $count_ori = Milestones::count();
        // Then create 2 more fake items
        factory(Milestones::class)->create();
        factory(Milestones::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a Milestones
     *
     * @return void
     */
    public function testMilestonesGet()
    {
        // Create fake relationships
        $task = factory(Tasks::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';
        $deadline = 1595375203;

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => $title,
                        'deadline' => $deadline,
                    ],
                    'relationships' => [
                        'jobs' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task->id,
                                    'priority' => 2,
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
        $this->commonSingleVndCheck($response, 'milestones');

        // Check Creation status
        $response->assertStatus(201);

        // Find created milestone
        $milestone = Milestones::orderBy('id', 'DESC')->first();

        // This should return a Milestones with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones/'.$milestone->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'milestones');

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
                    'deadline',
                ],
                'relationships' => [
                    'jobs' => [
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
                'type' => 'milestones',
                'attributes' => [
                    'title' => $title,
                    'deadline' => $deadline,
                ],
                'relationships' => [
                    'jobs' => [
                        'data' => [
                            [
                                'type' => 'tasks',
                                'meta' => [
                                    'priority' => '2',
                                ],
                            ],
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
            'jobs' => 'many',
        ];

        $this->commonRelationshipsCheck('milestones', $milestone->id, $relationships);
    }

    /**
     * Test to update a Milestones
     *
     * @return void
     */
    public function testMilestonesPatch()
    {
        // Create fake relationships
        $task_1 = factory(Tasks::class)->create();
        $task_2 = factory(Tasks::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';
        $deadline = 1595375203;

        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => $title,
                        'deadline' => $deadline,
                    ],
                    'relationships' => [
                        'jobs' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task_1->id,
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
        $this->commonSingleVndCheck($response, 'milestones');

        // Check Creation status
        $response->assertStatus(201);

        // Fake a new user to find
        $milestone = Milestones::orderBy('id', 'DESC')->first();

        // This should fail and call LinckoJson::error
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/milestones/'.$milestone->id,
            [
                'data' => [
                    'type' => 'milestones',
                    'id' => $milestone->id,
                    'attributes' => [
                        'title' => 'new title',
                    ],
                    'relationships' => [
                        'jobs' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task_2->id,
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
        $this->commonSingleVndCheck($response, 'milestones');

        // Check Success status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'milestones',
                'attributes' => [
                    'title' => 'new title',
                ],
                'relationships' => [
                    'jobs' => [
                        'data' => [
                            [
                                'type' => 'tasks',
                                'id' => $task_2->id,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a Milestones
     *
     * @return void
     */
    public function testMilestonesDelete()
    {
        // Fake a Milestone
        $milestone = factory(Milestones::class)->create();

        // Soft delete a Milestone
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/milestones/'.$milestone->id,
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
            '/brunoocto/sample/pm/milestones/'.$milestone->id,
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
            'message' => 'Resource milestones with id '.$milestone->id.' does not exist.',
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[with-trashed]=true',
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
                    'type' => 'milestones',
                    'id' => $milestone->id,
                    'attributes' => [
                        'title' => $milestone->title,
                    ],
                ],
            ],
        ]);

        // This should fail because the item is softed deleted
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[only-trashed]=true',
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
                    'type' => 'milestones',
                    'id' => $milestone->id,
                    'attributes' => [
                        'title' => $milestone->title,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test to delete a Milestones by force
     *
     * @return void
     */
    public function testMilestonesForceDelete()
    {
        // Create a Milestones that we will patch
        $milestone = factory(Milestones::class)->create();

        // Fail a forced deletion (Milestones uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/milestones/'.$milestone->id.'/force_delete',
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
            '/brunoocto/sample/pm/milestones?filter[with-trashed]=true',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check the resource is not reachable
        $response->assertStatus(200);

        // The number of Milestones should be 0
        $count = count($response->json()['data']);
        $this->assertEquals($count, 0);
    }

    /**
     * Test to restore a Milestones
     *
     * @return void
     */
    public function testMilestonesRestore()
    {
        // Fake a Milestones
        $milestone = factory(Milestones::class)->create();

        // Fail a deletion (Milestones uses hard deletion)
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/milestones/'.$milestone->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check Deletion status
        $response->assertStatus(204);

        // Fail a forced deletion (Milestones uses hard deletion)
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/milestones/'.$milestone->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'milestones',
                'id' =>  $milestone->id,
                'attributes' => [
                    'title' => $milestone->title,
                ],
            ],
        ]);
    }

    /**
     * Test filtering
     *
     * @return void
     */
    public function testMilestonesFilter()
    {

        // Create different items
        $milestone_1 = factory(Milestones::class)->create([
            'title' => 'bruno',
            'deadline' => 500,
        ]);
        $milestone_2 = factory(Milestones::class)->create([
            'title' => 'Martin',
            'deadline' => 1000,
        ]);
        $milestone_3 = factory(Milestones::class)->create([
            'title' => 'bruno',
            'deadline' => 1500,
        ]);
        $milestone_4 = factory(Milestones::class)->create([
            'title' => 'Martin',
            'deadline' => 2000,
        ]);

        // Test equal filtering, it should returns IDs: 2, 4
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[title]=Martin',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count = count($response->json()['data']);
        $this->assertEquals($count, 2);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'id' =>  $milestone_2->id,
                ],
                [
                    'id' =>  $milestone_4->id,
                ],
            ],
        ]);

        // Test <> filtering, it should returns IDs: 1, 3
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[title,"not%20like"]=Martin',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count = count($response->json()['data']);
        $this->assertEquals($count, 2);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'id' =>  $milestone_1->id,
                ],
                [
                    'id' =>  $milestone_3->id,
                ],
            ],
        ]);

        // Test OR filtering, it should returns IDs: 1, 4
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[deadline]=500&filter[or]&filter[deadline(1)]=2000',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count = count($response->json()['data']);
        $this->assertEquals($count, 2);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'id' =>  $milestone_1->id,
                ],
                [
                    'id' =>  $milestone_4->id,
                ],
            ],
        ]);

        // Test IN filtering, it should returns IDs: 3, 4
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[deadline,"in"]=1500,2000',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count = count($response->json()['data']);
        $this->assertEquals($count, 2);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'id' =>  $milestone_3->id,
                ],
                [
                    'id' =>  $milestone_4->id,
                ],
            ],
        ]);
    }

    /**
     * Test filtering
     *
     * @return void
     */
    public function testMilestonesFilterFail()
    {

        // Create different items
        $milestone_1 = factory(Milestones::class)->create([
            'title' => 'bruno',
            'deadline' => 500,
        ]);
        $milestone_2 = factory(Milestones::class)->create([
            'title' => 'Martin',
            'deadline' => 1000,
        ]);

        // Should fail because filter is missing the key
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[]=Martin',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count = count($response->json()['data']);
        $this->assertEquals($count, 2);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'id' =>  $milestone_1->id,
                ],
                [
                    'id' =>  $milestone_2->id,
                ],
            ],
        ]);

        // Should fail because filter is missing the key
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones?filter[title,"equal"]=Martin',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'milestones');

        // Check Read status
        $response->assertStatus(200);

        // Check total number
        $count = count($response->json()['data']);
        $this->assertEquals($count, 2);

        // Check some values
        $response->assertJson([
            'data' => [
                [
                    'id' =>  $milestone_1->id,
                ],
                [
                    'id' =>  $milestone_2->id,
                ],
            ],
        ]);
    }

    /**
     * Test included relationships
     *
     * @return void
     */
    public function testMilestonesIncluded()
    {
        // Create fake relationships
        $task = factory(Tasks::class)->create();

        // Set some variables to be checked
        $title = 'Léo 马丁';
        $deadline = 1595375203;

        // We use POST to make sure we get a database ID
        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/milestones',
            [
                'data' => [
                    'type' => 'milestones',
                    'attributes' => [
                        'title' => $title,
                        'deadline' => $deadline,
                    ],
                    'relationships' => [
                        'jobs' => [
                            'data' => [
                                [
                                    'type' => 'tasks',
                                    'id' => $task->id,
                                    'priority' => 2,
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
        $this->commonSingleVndCheck($response, 'milestones');

        // Check Creation status
        $response->assertStatus(201);

        // Find created milestone
        $milestone = Milestones::orderBy('id', 'DESC')->first();

        // This should return a Milestones with 1 NonLinears as parent
        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/milestones/'.$milestone->id.'?include=jobs',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'milestones');

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
                    'type' => 'tasks',
                    'id' => $task->id,
                    'attributes' => [
                        'title' => $task->title,
                    ],
                ],
            ],
        ]);
    }
}
