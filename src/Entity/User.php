<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\RequestLeave;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;


#[Entity]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
#[ORM\DiscriminatorMap(['user' => 'User', 'admin' => 'Admin'])]
class User {
    #[Id, GeneratedValue(strategy: "AUTO"), Column(type: "integer")]
    private $id;

    #[ORM\Column(type: 'string', name: 'userName')]
    private $userName;

    #[Column(type: "string")]
    private $department;

    #[ORM\Column(type: 'date', name: 'dateOfBirth')]
    private $dateOfBirth;

    #[Column(type: "string")]
    private $email;

    #[Column(type: "string")]
    private $password;

    #[Column(type: "string")]
    private $role;

    #[ORM\Column(type: 'integer', name: 'annualBalance')]
    private $annualBalance;

    #[OneToMany(targetEntity: RequestLeave::class, mappedBy: "user")]
    private $leaveRequests;

    public function __construct() 
    {
        $this->leaveRequests = new ArrayCollection();
    }

    public function getIsHr(){

        return $this->role === "admin";
    }
    public function addLeaveRequest(RequestLeave $leaveRequest): self 
    {
        if (!$this->leaveRequests->contains($leaveRequest)) {
            $this->leaveRequests[] = $leaveRequest;
            $leaveRequest->setUser($this);
        }

        return $this;
    }

    public function removeLeaveRequest(RequestLeave $leaveRequest): self 
    {
        if ($this->leaveRequests->removeElement($leaveRequest)) {
            if ($leaveRequest->getUser() === $this) {
                $leaveRequest->setUser(null);
            }
        }

        return $this;
    }

    public function consulterProfil() 
    {
        return [
            'username' => $this->userName,
            'department' => $this->department,
            'email' => $this ->email,
        ];
    }

    public function mettreAJourProfil($newData) 
    {
        $this->userName = $newData['username'];
        $this->department = $newData['department'];
        $this->dateOfBirth= $newData['dateofbirth'];
        $this->email= $newData['email'];
        $this->password= $newData['password'];
    }
    
    public function getUser(): ?User
    {  
        return $this;
    }
    public function getUserName(): ?string
    {
        return $this->userName;
    }
    public function setUserName($username) {
        $this->userName = $username;
    }
    public function getDepartment(): ?string
    {
        return $this->department;
    }
    public function setDepartment($department) {
        $this->department = $department;
    }
    public function setDateOfBirth($dateOfBirth) {
        $this->dateOfBirth = $dateOfBirth;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function getId(){
        return $this->id;
    }
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword($password) {
        $this->password = $password;
    }
    public function getRole(): ?string
    {
        return $this->role;
    }
    public function setRole($role) {
        $this->role = $role;
    }
    public function getAnnualBalance(): ?int
    {
        return $this->annualBalance;
    }
    // public function setAnnualBalance(int $days = 0): self
    // {
    //     if ($days === 0) {
    //         $now = new \DateTime('now');
    //         $birthDate = $this->dateOfBirth;
    //         $age = $now->diff($birthDate)->y;
    //         if ($age < 18) {
    //             $this->annualBalance = 24; 
    //         } elseif ($age >= 18 && $age <= 21) {
    //             $this->annualBalance = 18; 
    //         } else {
    //             $this->annualBalance = 12; 
    //         }
    //     } else {
    //         $this->annualBalance -= $days;
    //         if ($this->annualBalance < 0) {
    //             $this->annualBalance = 0;
    //         }
    //     }

    //     return $this;
    // }
    public function setAnnualBalance(int $days = 0): self
    {
        if ($days === 0) {
            $now = new \DateTime('now');
            $birthDate = $this->dateOfBirth;
            $age = $now->diff($birthDate)->y;
            if ($age < 18) {
                $this->annualBalance = 24; 
            } elseif ($age >= 18 && $age <= 21) {
                $this->annualBalance = 18; 
            } else {
                $this->annualBalance = 12; 
            }
        } else {
            $this->annualBalance = max(0, $days);
        }

        return $this;
    }
}
?>
