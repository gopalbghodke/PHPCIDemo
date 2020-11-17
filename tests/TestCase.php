<?php

namespace Brunoocto\Sample\Tests;

use Faker\Factory;
use Orchestra\Testbench\TestCase as TestbenchCase;
use Brunoocto\Json\Providers\JsonServiceProvider;
use CloudCreativity\LaravelJsonApi\ServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Brunoocto\Sample\Models\ProjectManagement\Users;
use Brunoocto\Sample\Providers\SampleServiceProvider;
use Brunoocto\Vmodel\Providers\VmodelServiceProvider;
use Brunoocto\Exception\Providers\ExceptionServiceProvider;
use Brunoocto\Filesystem\Providers\FilesystemServiceProvider;
use Brunoocto\Sample\Tests\SQLiteTestingConnector;

/**
 * TestCase for Service Provider
 */
class TestCase extends TestbenchCase
{
    /**
     * We cannot use refreshDatabase in memory here because of the following bugs:
     * 1) In the same "test" method, if we call more than once a request ->json(), any more call will fail because of some require_once in the Laravel boostrap. So we use refreshApplication();
     * 2) We can use $this->refreshApplication(), but it does launch the transaction rollback, so refreshDatabase cannot be used. So we use DatabaseMigrations.
     * 3) If we use DatabaseMigrations with Memory, refreshApplication delete the database, but not file version. So we use file version.
     */
    use DatabaseMigrations;

    /**
     * Instance of faker
     * Help to fake input data
     *
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * User used to Authenticate while using the header User-Id
     *
     * @var Users
     */
    protected $auth_user = null;

    /**
     * Setup launched at every test method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Load fake data to the Database
        $this->withFactories(__DIR__.'/../database/factories');
        // Instantiate the faker
        $this->faker = Factory::create();
        // Instantiate at least one user considerate as Authenticated user
        $this->auth_user = factory(Users::class)->create();
        // Simulate to have the header "User-Id"
        request()->headers->set('user-id', $this->auth_user->id);
    }

    /**
     * Check the basic structure of a Vnd Response with a single object
     *
     * @param Response $response  Json response of a call
     * @param string $type  Model type name
     * @return void
     */
    protected function commonSingleVndCheck($response, $type)
    {
        // Check the Status (2xx)
        $response->assertSuccessful();

        // Check the type is VND
        $response->assertHeader('Content-Type', 'application/vnd.api+json');

        // Check the minimum required Body structure
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
                'meta' => [
                    'checktime',
                ],
            ],
        ]);

        // Check if the type is correct
        $response->assertJson([
            'data' => [
                'type' => $type,
            ],
        ]);
    }

    /**
     * Check common relationship response structure
     *
     * @param string $type  Model type name
     * @param int $id  Model ID
     * @param array $relationships  Array of relationships to test
     * @return void
     */
    protected function commonRelationshipsCheck($type, $id, $relationships)
    {
        foreach ($relationships as $relationship => $relation) {
            $response = $this->json(
                'GET',
                '/brunoocto/sample/pm/'.$type.'/'.$id.'/relationships/'.$relationship,
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
            if ($relation == 'many') {
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
                '/brunoocto/sample/pm/'.$type.'/'.$id.'/'.$relationship,
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
            if ($relation == 'many') {
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
     * Check the basic structure of a Vnd Response with multiple objects
     *
     * @param Response $response  Json response of a call
     * @param string $type  Model type name
     * @return void
     */
    protected function commonMultipleVndCheck($response, $type)
    {
        // Check the Status (2xx)
        $response->assertSuccessful();

        // Check the type is VND
        $response->assertHeader('Content-Type', 'application/vnd.api+json');

        // Get number of users
        $count = count($response->json()['data']);
        // Prepare the structure to check
        $data = [];
        for ($i=0; $i < $count; $i++) {
            $data[] = [
                'type',
                'id',
                'attributes' => [
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
                'meta' => [
                    'checktime',
                ],
            ];
        }

        // Check the minimum required Body structure
        $response->assertJsonStructure([
            'data' => $data,
        ]);

        // Prepare the structure to check
        $data = [];
        for ($i=0; $i < $count; $i++) {
            $data[] = [
                'type' => $type,
            ];
        }

        // Check if the ype is correct
        $response->assertJson([
            'data' => $data,
        ]);
    }

    /**
     * Get package providers.
     * @param  Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SampleServiceProvider::class,
            ExceptionServiceProvider::class,
            VmodelServiceProvider::class,
            JsonServiceProvider::class,
            FilesystemServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    /**
     * Call the given URI with a JSON request.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        // Wait few milliseconds to insure the memory is filled with data
        usleep(100000);

        // Include Application refresh after every call to insure any second call in a test method does not bug
        // NOTE: We need to observe if any PHPunit regresseion possible, but I think it should not happen.
        $this->refreshApplication();
        // Factories need to be reload too
        $this->withFactories(__DIR__.'/../database/factories');

        // LinckoJson error method does not sent output while testing, it does not stop the code as expected. We need to force to stop the code by throwing an Exception. Just be aware that in such case the output does not reflect what the error should return.
        // https://emarketbot.readthedocs.io/en/latest/reference/expectations.html
        \LinckoJson::shouldReceive('error')->atLeast()->times(0)->andThrow(new \Exception('LinckoJson::error Mockery Exception', 400));

        // Call request
        return parent::json($method, $uri, $data, $headers);
    }

    /**
     * Initialize environment
     * @param mixed $app
     * @return void
     */
    protected function getEnvironmentSetup($app)
    {
        /**
         *  To enable the memory database not constantly refreshing, we overwrite the registrering process of ":memory:" to keep one single connection after application refresh call.
         * using ":shared-memory:" does not work as describe in the below link because of SQLiteBuilder@dropAllTables which works only with ":memory:".
         * https://qiita.com/crhg/items/c53e9381f6c976f211c1
         */
        $app->singleton('db.connector.sqlite', SQLiteTestingConnector::class);

        // Configure Temporary Test database (note that it can be slow to run all tests within big project, but optimizations are possible)
        $app['config']->set('database.default', 'brunoocto_sample');
        $app['config']->set('database.connections.brunoocto_sample', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => 'sample_',
            'foreign_key_constraints' => !is_null(env('LINCKO_SAMPLE_DB_FOREIGN_KEYS')) ? env('LINCKO_SAMPLE_DB_FOREIGN_KEYS') : env('DB_FOREIGN_KEYS', false),
        ]);

        /**
         *
         * Note that if you want to overwrite a configuration, do it on array, not its value
         *
         * This won't work:
         * $app['config']->set('filesystems.disks.local.root', storage_path('app'));
         *
         * This works:
         * $app['config']->set('filesystems.disks.local', [
         *   'driver' => 'local',
         *   'root' => storage_path('app'),
         * ]);
         *
         */
    }

    /**
     * Initialize Aliases
     *
     * @param mixed $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'SampleAlias' => 'Brunoocto\Sample\Facades\SampleFacade',
            'LinckoVmodel' => 'Brunoocto\Vmodel\Facades\VmodelFacade',
            'LinckoJson' => 'Brunoocto\Json\Facades\JsonFacade',
            'JsonApi' => 'CloudCreativity\LaravelJsonApi\Facades\JsonApi',
        ];
    }
}
