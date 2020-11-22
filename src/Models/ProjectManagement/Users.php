<?php

namespace Brunoocto\Sample\Models\ProjectManagement;

use Brunoocto\Vmodel\Models\VmodelModel;

/**
 * Users model
 * (NOTE: Relationship might not follow a real business logic, but this is only for example)
 *
 * Tables needed:
 *  - 'users' => The model storage
 *
 */
class Users extends VmodelModel
{
    /**
     * Storage validation rules
     *
     * @var array
     */
    protected static $rules = [
        'email' => 'required|unique:users,email|max:190|email:rfc,dns',
        'name' => 'max:256',
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
    protected $table = 'users';

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
        'email',
        'name',
    ];

    /**
     * Force the output format of some keys
     *
     * @var array
     */
    protected $casts = [
        'email' => 'string',
        'name' => 'string',
    ];

    /**
     * Default CRUD restriction per model class
     * 0100:  R
     * 1100: CR
     * 1110: CRU
     * 1111: CRUD (default)
     *
     *  @var array
     */
    protected $crud = '1100';

    /**
     * Default CRUD restriction per model class for the owner
     * 0100:  R
     * 1100: CR
     * 1110: CRU
     * 1111: CRUD (default)
     *
     *  @var array
     */
    protected $crud_owner = '1111';

    /**
     * Get the binary item CRUD for the current user
     *
     * @return string CRUD
     */
    public function getCRUD()
    {
        // Get User ID
        $user_id = $this->getAuthUserId();

        // For the user itself, created_by is replaced by id
        if ($this->id == $user_id) {
            return $this->crud_owner;
        }

        return parent::getCRUD();
    }

    /**
     * Save the model into the database.
     * It help to avoid a compatibility issue with password field
     *
     * @return boolean
     */
    public function save(array $options = [])
    {
        // If it's a new item without password given
        if (!isset($this->id) && !isset($this->password)) {
            // We set the column 'password' only for compatbility with Laravel Authentication predefined users table but it is not used in the sample Model
            $this->password = 'some_password';
        }

        return parent::save($options);
    }

    /**
     * Disable Deletion
     *
     * @return false
     */
    public function delete()
    {
        \LinckoJson::error(405, 'Operation not allowed.');
        return false;
    }

    /**
     * Disable Restoration
     *
     * @return false
     */
    public function restoreItem()
    {
        \LinckoJson::error(405, 'Operation not allowed.');
        return false;
    }
}
