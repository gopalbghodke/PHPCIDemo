<?php

namespace Brunoocto\Sample\Controllers;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Brunoocto\Sample\Models\Sample;
use Brunoocto\Sample\Services\SampleService;
use Brunoocto\Sample\Contracts\SampleInterface;

class SampleController extends Controller
{
    /**
     * Dependence injection
     * $sample will be instanciated according to the binding rule in the provider,
     * or simply by doing a "new SampleService" if no binding.
     * This method is suitable only for Service or Model that are specific tp the project itself,
     * and that we know that there won't be a replacement service possible, like a Model for a DB table.
     *
     * Pros:
     *   - Easy to setup
     *   - Easy to understand
     *   - Mock $sample in Unit test is simple.
     *
     * Cons:
     *   - It is fixed to a specific service. It makes difficult to switch to another library because we need to import the service itself each time we want to use it within the application.
     *
     * @param SampleService $sample
     * @return Response
     */
    public function putDependency(SampleService $sample)
    {
        return $sample->json('Dependency injection');
    }

    /**
     * Interface dependency injection
     * $sample will be instanciated according to the binding rule in the provider,
     * or simply by doing a "new SampleService" if no binding.
     * This method is suitable for shared services between project that we can easily mock.
     * And the contract (Interface) makes it easy to swap another service, we just need to change it in the provider.
     *
     * Pros:
     *   - Can design a shareable service for other projects
     *   - Very easy to swap to another service
     *   - Mock $sample in Unit test is simple.
     *
     * Cons:
     *   - Complex to understand how it works
     *   - Complex to setup
     *
     * @param SampleService $sample
     * @return Response
     */
    public function putInterface(SampleInterface $sample)
    {
        return $sample->json('Interface Dependency injection');
    }

    /**
     * Facade with Interface
     * A Facade is convenient to be called everywhere in the application.
     * It can be seen as a global instance (usually as a singleton) to access a logic.
     * This method is suitable for shared services between project with the advantage of a clean code.
     * Combined with Interface, it makes easier to swap the service.
     *
     * Pros:
     *   - Clean code
     *   - Easy to use
     *
     * Cons:
     *   - Complex to setup
     *   - Unusual because a Facade is a instanciated class, but methods are called like static ones but there are not.
     *   - Not design to be mocked for unit test
     *
     * @return Response
     */
    public function putFacade()
    {
        return \SampleAlias::json('Facade with Interface');
    }

    /**
     * Maker with Interface
     * Maker is the same as a Facade, it's slighty more verbose, but the way to use it is more natural.
     * Other comments are same as the Facade method thus.
     *
     * Pros:
     *   - Clean code
     *   - Easier to understand than Facade
     *   - Compare to Facade, methods are normally used.
     *
     * Cons:
     *   - More verbose than Facade
     *   - Not design to be mocked for unit test
     *
     * @return Response
     */
    public function putMaker()
    {
        return app()->make('sample_interface')->json('Maker with Interface');
    }

    /**
     * A route to test some code
     *
     * @return Response
     */
    public function postSample(Request $request, Sample $sample)
    {
        $sample->text = $request->input('data.attributes.text');
        $sample->save();

        return \SampleAlias::json('Create Sample', 201, 'Post created');
    }

    /**
     * A route to test some code
     *
     * @return Response
     */
    public function putTest()
    {
        // Write your code here to test what you need
        /*
            [...]
        */
        return (new SampleService)->json('Controller for test only');
    }
}
