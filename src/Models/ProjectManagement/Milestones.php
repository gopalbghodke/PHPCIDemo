<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Illuminate\Database\Eloquent\SoftDeletes;
use Brunoocto\Vmodel\Models\VmodelModel;

use Brunoocto\Sample\Models\ProjectManagement\Tasks;

/**
 * Milestones model
 * (NOTE: Relationship might not follow a real business logic, but this is only for example)
 *
 * Tables needed:
 *  - 'Milestones' => The model storage
 *  - 'task_x_milestone' => Pivot ManyToMany
 *
 */
class Milestones extends VmodelModel
{
    // Add "deleted_at"
    use SoftDeletes;

    /**
    * All Relationships
    *
    * @var array
    */
    protected static $model_relationships = [
        // Editable (usually UP relation)
        // NOTE: The display name is not necessarely the same as the table name, we can customize as we wish (but the type of the object must be correct)
        'jobs' => [ // Display name (always plural)
            true, // Authorize input
            true, // Visible
            'tasks', // Method name
            'belongsToMany', // Relation type
            Tasks::class, // Class name
        ],
    ];

    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required|max:1000',
        'deadline' => 'date_format:U,date_format:U.u',
    ];

    /**
     * Database connection
     *
     * @var string
     */
    protected $connection = 'brunoocto_sample';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'milestones';

    /**
     * List visible keys for json response.
     * Anything starting with a single underscore are reserved for relationships.
     * VmodelModel does always add by default the following fields if they exist in the table:
     *  'id',
     *  'created_at',
     *  'created_by',
     *  'updated_at',
     *  'updated_by',
     *  'deleted_at',
     *  'deleted_by',
     *
     * @var array
     */
    protected $visible = [
        'title',
        'deadline',
    ];

    /**
     * Force the output format of some keys
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'deadline' => 'datetime:U',
    ];

    /* -------- Start - Relationships UP -------- */

    /**
     * Get all Tasks that own the Milestones.
     * Many(Tasks) to Many(Milestones)
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Tasks::Collection
     */
    public function tasks()
    {
        return $this->belongsToMany(Tasks::class, 'task_x_milestone', 'milestone_id', 'task_id')->withPivot('priority');
    }

    /* -------- End - Relationships -------- */
}
