<?php

namespace App\Entity;

use App\Repository\CandidateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile = null;

    #[ORM\ManyToOne(inversedBy: 'candidates')]
    private ?Degree $degree = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $resume = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $applicationDate = null;

    #[ORM\Column(length: 255, nullable: true)]   
    private $resumeFileType = null;

    #[ORM\Column(length: 255, nullable: true)]   
    private $resumeFileExtension = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getDegree(): ?Degree
    {
        return $this->degree;
    }

    public function setDegree(?Degree $degree): self
    {
        $this->degree = $degree;

        return $this;
    }

    public function getResume()
    {
        return $this->resume;
    }

    public function setResume($resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getApplicationDate(): ?\DateTimeInterface
    {
        return $this->applicationDate;
    }

    public function setApplicationDate(\DateTimeInterface $applicationDate): self
    {
        $this->applicationDate = $applicationDate;

        return $this;
    }

    public function getResumeFileType(): ?string
    {
        return $this->resumeFileType;
    }

    public function setResumeFileType(?string $resumeFileType): self
    {
        $this->resumeFileType = $resumeFileType;

        return $this;
    }

    public function getResumeFileExtension(): ?string
    {
        return $this->resumeFileExtension;
    }

    public function setResumeFileExtension(?string $resumeFileExtension): self
    {
        $this->resumeFileExtension = $resumeFileExtension;

        return $this;
    }
}
