<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Illuminate\Database\Eloquent\SoftDeletes;
use Brunoocto\Vmodel\Models\VmodelModel;

use Brunoocto\Sample\Models\ProjectManagement\Projects;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;
use Brunoocto\Sample\Models\ProjectManagement\Comments;
use Brunoocto\Sample\Models\ProjectManagement\NonLinears;

/**
 * Files model
 * (NOTE: Relationship might not follow a real business logic, but this is only for example)
 *
 * Tables needed:
 *  - 'files' => The model storage
 *  - 'other_x_file' => Pivot ManyToMany Polymorphic
 *
 */
class Files extends VmodelModel
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
        'projects' => [ // Display name (always plural)
            true, // Authorize input
            true, // Visible
            'projects', // Method name
            'morphedByMany', // Relation type
            Projects::class, // Class name
        ],
        'tasks' => [
            true,
            true,
            'tasks',
            'morphedByMany',
            Tasks::class,
        ],
        'comments' => [
            true,
            true,
            'comments',
            'morphedByMany',
            Comments::class,
        ],
        'non_linears' => [
            true,
            true,
            'nonLinears',
            'morphedByMany',
            NonLinears::class,
        ],
        // Non-editable (usually DOWN relation)
        'children_comments' => [
            false,
            true,
            'childrenComments', // Must be camelCase of the display name
            'hasMany',
            Comments::class,
        ],
    ];

    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required|max:1000',
        'mime' => 'required|max:100',
        'bytes' => 'required|integer|min:1',
        'path' => 'required|max:1000',
        'relationships' => 'required',
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
    protected $table = 'files';

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
        'mime',
        'bytes',
        'path',
        'width',
        'height',
    ];

    /* -------- Start - Relationships UP -------- */

    /**
     * Get all Projects that own the Files.
     * Many(Projects) to Many(Files) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Projects::Collection
     */
    public function projects()
    {
        /**
         * Projects::class => The class name of the Relation
         * 'other' => Does define the prefix {prefix}_id, {prefix}_type, but it is unused here since we redeclare them in later parameters
         * 'files_x_other' => Pivot table name
         * 'file_id' => This is the ID of the relation (Files)
         * 'other_id' => This is the ID of current model (Projects)
         * withPivot('visibility') => adding 'visibility' does add this relation value (specific to the combo) to the result
         */
        return $this->morphedByMany(Projects::class, 'other', 'other_x_file', 'file_id', 'other_id')->withPivot('visibility');
    }

    /**
     * Get all Tasks that own the Files.
     * Many(Tasks) to Many(Files) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Tasks::Collection
     */
    public function tasks()
    {
        return $this->morphedByMany(Tasks::class, 'other', 'other_x_file', 'file_id', 'other_id')->withPivot('visibility');
    }

    /**
     * Get all Comments that own the Files.
     * Many(Comments) to Many(Files) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Comments::Collection
     */
    public function comments()
    {
        return $this->morphedByMany(Comments::class, 'other', 'other_x_file', 'file_id', 'other_id')->withPivot('visibility');
    }

    /**
     * Get all Comments that own the Files.
     * Many(NonLinears) to Many(Files) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\NonLinears::Collection
     */
    public function nonLinears()
    {
        return $this->morphedByMany(NonLinears::class, 'other', 'other_x_file', 'file_id', 'other_id')->withPivot('visibility');
    }

    /* -------- Start - Relationships DOWN -------- */

    /**
     * Get all Comments that belong to the Tasks.
     * One(Files) to Many(Comments) Polymorphic
     *
     * @return Brunoocto\Sample\Models\ProjectManagement\Comments::Collection
     */
    public function childrenComments()
    {
        return $this->morphMany(Comments::class, 'other', 'other_type', 'other_id');
    }

    /* -------- End - Relationships -------- */
}
