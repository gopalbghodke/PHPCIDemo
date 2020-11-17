<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Brunoocto\Vmodel\JsonApi\VmodelAdapterSoftDeletes;
use Brunoocto\Vmodel\Models\VmodelModel;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use CloudCreativity\LaravelJsonApi\Routing\Route;
use Brunoocto\Sample\Models\ProjectManagement\Projects;

/**
 * Projects Adapater
 * If we need a specific Adapter for the model
 *
 */
class ProjectsAdapter extends VmodelAdapterSoftDeletes
{
    // DO NOT DELETE THIS CLASS
    // This class does nothing, it's just for test and showing that it can exists

    /**
     * Adapter constructor.
     *
     * @param VmodelModel $model
     * @param StandardStrategy $paging (cannot use PagingStrategyInterface here, not instantiable)
     * @param Route $route
     *
     * @return void
     */
    public function __construct(VmodelModel $model, StandardStrategy $paging, Route $route)
    {
        // We set a variable instead of using a parameter because we don't have the hand of the method called in the third library. It's unusual, but it still work
        static::setModelClassConstructor(Projects::class);

        // Parent constructor
        parent::__construct($model, $paging, $route);
    }
}
