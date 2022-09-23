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
    private $id;

    /**
     * @var string
     */
    private $no_employee;

    /**
     * @var string
     */
    private $full_name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $personal_phone;

    /**
     * @var string 
     */
    private $office_phone;

    /**
     * @var string
     */
    private $position;

    /**
     * @var string 
     */
    private $area;

    /**
     * @var int 
     */
    private $status_id;

    /**
     * @var int 
     */
    private $role_id;

    /**
     * @var RoleDTO
     */
    private $role;

    /**
     * @var int
     */
    private $office_id;

    /**
     * @var OfficeDTO
     */
    private $office;

    /**
     * @throws CustomErrorException
     */
    public function __construct(array $data = [])
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNoEmployee(): string
    {
        return $this->no_employee;
    }

    /**
     * @param string $no_employee
     */
    public function setNoEmployee(string $no_employee)
    {
        $this->no_employee = $no_employee;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->full_name;
    }

    /**
     * @param string $full_name
     */
    public function setFullName(string $full_name)
    {
        $this->full_name = $full_name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPersonalPhone(): string
    {
        return $this->personal_phone;
    }

    /**
     * @param string $personal_phone
     */
    public function setPersonalPhone(string $personal_phone)
    {
        $this->personal_phone = $personal_phone;
    }

    /**
     * @return string
     */
    public function getOfficePhone(): string
    {
        return $this->office_phone;
    }

    /**
     * @param string $office_phone
     */
    public function setOfficePhone(string $office_phone)
    {
        $this->office_phone = $office_phone;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position)
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getArea(): string
    {
        return $this->area;
    }

    /**
     * @param string $area
     */
    public function setArea(string $area)
    {
        $this->area = $area;
    }

    /**
     * @return int
     */
    public function getStatusId(): int
    {
        return $this->status_id;
    }

    /**
     * @param int $status_id
     */
    public function setStatusId(int $status_id)
    {
        $this->status_id = $status_id;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @param int $role_id
     */
    public function setRoleId(int $role_id)
    {
        $this->role_id = $role_id;
    }

    /**
     * @return RoleDTO
     */
    public function getRole(): RoleDTO
    {
        return $this->role;
    }

    /**
     * @param RoleDTO $role
     */
    public function setRole(RoleDTO $role)
    {
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getOfficeId(): int
    {
        return $this->office_id;
    }

    /**
     * @param int $office_id
     */
    public function setOfficeId(int $office_id)
    {
        $this->office_id = $office_id;
    }

    /**
     * @return OfficeDTO
     */
    public function getOffice(): OfficeDTO
    {
        return $this->office;
    }

    /**
     * @param OfficeDTO $office
     */
    public function setOffice(OfficeDTO $office)
    {
        $this->office = $office;
    }
}