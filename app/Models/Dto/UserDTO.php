<?php

namespace App\Models\Dto;

class UserDTO
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $no_employee;

    /**
     * @var string|null
     */
    private $full_name;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $personal_phone;

    /**
     * @var string|null 
     */
    private $office_phone;

    /**
     * @var string|null
     */
    private $position;

    /**
     * @var string|null 
     */
    private $area;

    /**
     * @var int|null 
     */
    private $status_id;

    /**
     * @var int|null 
     */
    private $role_id;

    /**
     * @var int|null
     */
    private $office_id;

    /**
     * @return int|null
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getNoEmployee(): string
    {
        return $this->no_employee;
    }

    /**
     * @param string|null $no_employee
     */
    public function setNoEmployee(string $no_employee)
    {
        $this->no_employee = $no_employee;
    }

    /**
     * @return string|null
     */
    public function getFullName(): string
    {
        return $this->full_name;
    }

    /**
     * @param string|null $full_name
     */
    public function setFullName(string $full_name)
    {
        $this->full_name = $full_name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPersonalPhone(): string
    {
        return $this->personal_phone;
    }

    /**
     * @param string|null $personal_phone
     */
    public function setPersonalPhone(string $personal_phone)
    {
        $this->personal_phone = $personal_phone;
    }

    /**
     * @return string|null
     */
    public function getOfficePhone(): string
    {
        return $this->office_phone;
    }

    /**
     * @param string|null $office_phone
     */
    public function setOfficePhone(string $office_phone)
    {
        $this->office_phone = $office_phone;
    }

    /**
     * @return string|null
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     */
    public function setPosition(string $position)
    {
        $this->position = $position;
    }

    /**
     * @return string|null
     */
    public function getArea(): string
    {
        return $this->area;
    }

    /**
     * @param string|null $area
     */
    public function setArea(string $area)
    {
        $this->area = $area;
    }

    /**
     * @return int|null
     */
    public function getStatusId(): int
    {
        return $this->status_id;
    }

    /**
     * @param int|null $status_id
     */
    public function setStatusId(int $status_id)
    {
        $this->status_id = $status_id;
    }

    /**
     * @return int|null
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @param int|null $role_id
     */
    public function setRoleId(int $role_id)
    {
        $this->role_id = $role_id;
    }

    /**
     * @return int|null
     */
    public function getOfficeId(): int
    {
        return $this->office_id;
    }

    /**
     * @param int|null $office_id
     */
    public function setOfficeId(int $office_id)
    {
        $this->office_id = $office_id;
    }
}