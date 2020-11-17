<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use Brunoocto\Sample\Models\ProjectManagement\Fails;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Fails Adapter
 * IMPORTANT: This class is design to force a failure because it is not extending from VmodelAdapter
 *
 */
class FailsAdapter extends AbstractAdapter
{
    /**
     * Mapping of JSON API attribute field names to model keys.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Resource relationship fields that can be filled.
     *
     * @var array
     */
    protected $relationships = [];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new Fails(), $paging);
    }

    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        // TODO
    }
}
