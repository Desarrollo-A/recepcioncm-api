<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class UserDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $no_employee;

    /**
     * @var string
     */
    public $full_name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * Variable para la contraseña actual
     * @var string
     */
    public $currentPassword;

    /**
     * @var string
     */
    public $personal_phone;

    /**
     * @var string 
     */
    public $office_phone;

    /**
     * @var string
     */
    public $position;

    /**
     * @var string 
     */
    public $area;

    /**
     * @var int 
     */
    public $status_id;

    /**
     * @var int 
     */
    public $role_id;

    /**
     * @var RoleDTO
     */
    public $role;

    /**
     * @var int
     */
    public $office_id;

    /**
     * @var int
     */
    public $department_manager_id;

    /**
     * @var OfficeDTO
     */
    public $office;

    /**
     * @var string[]
     */
    public $managers;

    /**
     * @var bool
     */
    public $is_office_manager;

    /**
     * @throws CustomErrorException
     */
    public function __construct(array $data = [])
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }
}