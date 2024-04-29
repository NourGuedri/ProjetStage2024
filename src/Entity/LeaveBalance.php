<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use App\Entity\User;
#[Entity]
class LeaveBalance
{

    #[Id]
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private $user;

    #[Id]
    #[ManyToOne(targetEntity: LeaveType::class)]
    #[JoinColumn(name: "leave_type_id", referencedColumnName: "id", nullable: false)]
    private $leaveType;


    #[Column(type: "integer")]
    private $remaining;

    #[Column(type: "integer")]
    private $consumed;



    public function getLeaveType(): ?LeaveType
{
    return $this->leaveType;
}

public function setLeaveType(?LeaveType $leaveType): self
{
    $this->leaveType = $leaveType;

    return $this;
}

public function setRemaining(int $remaining): self
{
    if ($remaining < 0) {
        throw new \InvalidArgumentException('Remaining leave balance cannot be negative');
    }
    $this->remaining = $remaining;

    return $this;
}

public function setConsumed(int $consumed): self
{
    // If no value is provided, set consumed to 0.
    if (null === $consumed) {
        $consumed = 0;
    } elseif ($consumed < 0) {
        throw new \InvalidArgumentException('Consumed leave balance cannot be negative.');
    }

    $this->consumed = $consumed;

    return $this;
}
  
public function setUser(?User $user): self
{
    $this->user = $user;

    return $this;
}

public function getRemaining(): ?int
{
    return $this->remaining;

}

public function getConsumed(): ?int
{
    return $this->consumed;
}
}