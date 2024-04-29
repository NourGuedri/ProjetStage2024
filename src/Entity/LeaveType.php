<?php

// namespace App\Entity;

// use Doctrine\ORM\Mapping\Entity;
// use Doctrine\ORM\Mapping\Id;
// use Doctrine\ORM\Mapping\GeneratedValue;
// use Doctrine\ORM\Mapping\Column;
// use Doctrine\ORM\Mapping\OneToMany;
// use Doctrine\ORM\Mapping\OneToOne;
// use Doctrine\Common\Collections\ArrayCollection;

// #[Entity]
// class LeaveType {
//     #[Id, GeneratedValue(strategy: "AUTO"), Column(type: "integer")]
//     private $id;

//     #[Column(type: "string")]
//     private $description;

//     #[Column(type: "string")]
//     private $name;

//     #[Column(type: "integer")]
//     private $max_days;

//     // a constructor
//     public function __construct($description,$name,$max_days) 
//     {
//         $this->description = $description;
//         $this->name = $name;
//         $this->max_days = $max_days;
        
//     }

//     // Getters and setters
//     public function getId(): ?int
//     {
//         return $this->id;
//     }
    

//     public function getDescription(): ?string
//     {
//         return $this->description;
//     }
//     public function setDescription(string $description): self
//     {
//         $this->description = $description;
//         return $this;
//     }  
    
//     public function getName(): string
//     {
//         return $this->name;
//     }
//     public function setName(string $name): self
//     {
//         $this->name = $name;
//         return $this;
//     }
//     public function getMaxDays(): int
//     {
//         return $this->max_days;
//     }
//     public function setMaxDays(int $max_days): self
//     {
//         $this->max_days = $max_days;
//         return $this;
//     }




//     // Include other methods as needed
// }

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[Entity]
class LeaveType {
    #[Id, GeneratedValue(strategy: "AUTO"), Column(type: "integer")]
    private $id;

    #[Column(type: "string")]
    private $description;

    #[Column(type: "string")]
    private $name;

    #[Column(type: "integer")]
    private $max_days;

    #[OneToMany(mappedBy: 'leaveType', targetEntity: RequestLeave::class, cascade: ['remove'])]
    private Collection $requestLeaves;

    #[OneToMany(mappedBy: 'leaveType', targetEntity: LeaveBalance::class, cascade: ['remove'])]
    private Collection $leaveBalances;

    public function __construct($description,$name,$max_days) 
    {
        $this->description = $description;
        $this->name = $name;
        $this->max_days = $max_days;
        $this->requestLeaves = new ArrayCollection();
        $this->leaveBalances = new ArrayCollection();
    }

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }
    

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }  
    
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getMaxDays(): int
    {
        return $this->max_days;
    }
    public function setMaxDays(int $max_days): self
    {
        $this->max_days = $max_days;
        return $this;
    }

    // Getters and setters for the new relationships
    public function getRequestLeaves(): Collection
    {
        return $this->requestLeaves;
    }

    public function getLeaveBalances(): Collection
    {
        return $this->leaveBalances;
    }

    // Include other methods as needed
}
?>
