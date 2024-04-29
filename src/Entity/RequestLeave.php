<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;



#[Entity]
class RequestLeave
{
    #[Id, GeneratedValue(strategy: "AUTO"), Column(type: "integer")]
    private $id;

    #[Column(type: "date")]
    private $startDate;

    #[Column(type: "string")]
    private $status = "pending";

    #[Column(type: "string")]
    private $reason;

    #[Column(type: "date")]
    private $endDate;

    #[ManyToOne(targetEntity: User::class, inversedBy: "leaveRequests")]
    private $user;

    #[ManyToOne(targetEntity: LeaveType::class)]
    #[JoinColumn(nullable: false)]
    private $leaveType;




    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setLeaveType(?LeaveType $leaveType): self
    {
        $this->leaveType = $leaveType;
        return $this;
    }
    public function getLeaveType(): ?LeaveType
    {
        return $this->leaveType;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }
    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }
    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $allowedStatuses = ["pending", "approved", "rejected"];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException(sprintf('Invalid status "%s". Allowed values are: "%s"', $status, implode('", "', $allowedStatuses)));
        }
        $this->status = $status;
        return $this;
    }
    public function getReason(): ?string
    {
        return $this->reason;
    }
    public function setReason(string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }
    public function getLeaveDays(): int
    {
        // Assuming startDate and endDate are \DateTime objects
        $interval = $this->startDate->diff($this->endDate);

        // $interval->days gives the total number of days between the two dates
        // Add 1 to include both the start and end dates in the count
        return $interval->days + 1;
    }
}
