<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Brunoocto\Vmodel\JsonApi\VmodelValidators;
use Brunoocto\Vmodel\JsonApi\VmodelAdapter;
use Brunoocto\Sample\Models\ProjectManagement\Projects;
use CloudCreativity\LaravelJsonApi\Factories\Factory;
use CloudCreativity\LaravelJsonApi\Contracts\ContainerInterface;

/**
 * Projects Validator
 * If we need a specific Validator for the model
 *
 */
class ProjectsValidators extends VmodelValidators
{
    // DO NOT DELETE THIS CLASS
    // This class does nothing, it's just for test and showing that it can exists

    /**
     * Constructor.
     *
     * @param Factory $factory
     * @param ContainerInterface $container
     */
    public function __construct(Factory $factory, ContainerInterface $container)
    {
        // We set a variable instead of using a parameter because we don't have the hand of the method called in the third library. It's unusual, but it still work
        VmodelAdapter::setModelClassConstructor(Projects::class);

        // Parent constructor
        parent::__construct($factory, $container);
    }
}
