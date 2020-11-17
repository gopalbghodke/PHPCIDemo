<?php

namespace Brunoocto\Sample\Tests\Feature\ProjectManagement;

use Brunoocto\Sample\Tests\TestCase;
use Brunoocto\Sample\Models\ProjectManagement\Users;

class UsersFeatureTest extends TestCase
{
    /**
     * Test to create a user
     *
     * @return void
     */
    public function testUsersPost()
    {
        // Set some variables to be checked
        $name = 'Léo 马丁';
        $email = 'some.one@lincko.com';

        $response = $this->json(
            'POST',
            '/brunoocto/sample/pm/users',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'name' => $name,
                        'email' => $email,
                    ],
                ],
            ],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
            ]
        );
        
        // Check basic response structure
        $this->commonSingleVndCheck($response, 'users');

        // Check Creation status
        $response->assertStatus(201);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'name' => $name,
                    'email' => $email,
                ]
            ],
        ]);
    }

    /**
     * Test to get multiple users
     *
     * @return void
     */
    public function testUsersAllGet()
    {
        $count_ori = Users::count();
        // Then create 2 more fake users
        factory(Users::class)->create();
        factory(Users::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/users',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonMultipleVndCheck($response, 'users');

        // Check Read status
        $response->assertStatus(200);

        // Check that we have 3 users in total
        $count_new = count($response->json()['data']);
        $this->assertEquals($count_new, $count_ori + 2);
    }

    /**
     * Test to get a user
     *
     * @return void
     */
    public function testUsersGet()
    {
        // Fake a new user to find
        $user = factory(Users::class)->create();

        $response = $this->json(
            'GET',
            '/brunoocto/sample/pm/users/'.$user->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check basic response structure
        $this->commonSingleVndCheck($response, 'users');

        // Check Read status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'users',
                'id' => $user->id,
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ],
        ]);
    }

    /**
     * Test to update a user
     *
     * @return void
     */
    public function testUsersPatch()
    {
        // Set some variables to be checked
        $name = 'Léo 马丁';

        // Try a successful update
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/users/'.$this->auth_user->id,
            [
                'data' => [
                    'id' => $this->auth_user->id,
                    'type' => 'users',
                    'attributes' => [
                        'name' => $name,
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
        $this->commonSingleVndCheck($response, 'users');

        // Check Update status
        $response->assertStatus(200);

        // Check some values
        $response->assertJson([
            'data' => [
                'type' => 'users',
                'id' => $this->auth_user->id,
                'attributes' => [
                    'name' => $name,
                ]
            ],
        ]);
    }

    /**
     * Test to fail updating a user
     *
     * @return void
     */
    public function testUsersPatchFail()
    {
        // Set some variables to be checked
        $name = 'John Wu';

        // Fail (cannot update someone else)
        // Fake a new user to find
        $user = factory(Users::class)->create();
        $response = $this->json(
            'PATCH',
            '/brunoocto/sample/pm/users/'.$user->id,
            [
                'data' => [
                    'id' => $user->id,
                    'type' => 'users',
                    'attributes' => [
                        'name' => $name,
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
     * Test to fail deleting a user
     *
     * @return void
     */
    public function testUsersDeleteFail()
    {
        $this->expectException('Exception');
        // Try to fail a deletion
        $response = $this->json(
            'DELETE',
            '/brunoocto/sample/pm/users/'.$this->auth_user->id,
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // Check that an expection is sent
        $this->assertTrue(isset($response->exception));

        // Fake a new user
        $user = factory(Users::class)->create();
        // The next call will use LinckoJson::error which does throw an exception (mockery)
        $this->expectException('Exception');
        $user->delete();
        // NOTE: Because of the exception, no following code will be run, teh test will end here
    }

    /**
     * Test to fail deleting a user by force
     *
     * @return void
     */
    public function testUsersForceDeleteFail()
    {
        // Try to fail a forced deletion
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/users/'.$this->auth_user->id.'/force_delete',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // The route should not exists
        $response->assertStatus(404);

        // Fake a new user
        $user = factory(Users::class)->create();
        // The next call will use LinckoJson::error which does throw an exception (mockery)
        $this->expectException('Exception');
        $user->forceDelete();
        // NOTE: Because of the exception, no following code will be run, teh test will end here
    }

    /**
     * Test to fail restoring a user
     *
     * @return void
     */
    public function testUsersRestoreFail()
    {
        // Try a successful update
        $response = $this->json(
            'PUT',
            '/brunoocto/sample/pm/users/'.$this->auth_user->id.'/restore',
            [],
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'User-Id' => $this->auth_user->id,
            ]
        );

        // The route should not exists
        $response->assertStatus(404);

        // Fake a new user
        $user = factory(Users::class)->create();
        // The next call will use LinckoJson::error which does throw an exception (mockery)
        $this->expectException('Exception');
        $user->restoreItem();
        // NOTE: Because of the exception, no following code will be run, teh test will end here
    }
}
