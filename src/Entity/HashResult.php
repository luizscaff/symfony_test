<?php

namespace App\Entity;

use App\Repository\HashResultRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HashResultRepository::class)
 */
class HashResult
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $batch;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $input_string;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $solution_key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(type="bigint")
     */
    private $number_of_tries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requisition_index;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBatch(): ?\DateTimeInterface
    {
        return $this->batch;
    }

    public function setBatch(\DateTimeInterface $batch): self
    {
        $this->batch = $batch;

        return $this;
    }

    public function getInputString(): ?string
    {
        return $this->input_string;
    }

    public function setInputString(string $input_string): self
    {
        $this->input_string = $input_string;

        return $this;
    }

    public function getSolutionKey(): ?string
    {
        return $this->solution_key;
    }

    public function setSolutionKey(string $solution_key): self
    {
        $this->solution_key = $solution_key;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getNumberOfTries(): ?string
    {
        return $this->number_of_tries;
    }

    public function setNumberOfTries(string $number_of_tries): self
    {
        $this->number_of_tries = $number_of_tries;

        return $this;
    }

    public function getRequisitionIndex(): ?int
    {
        return $this->requisition_index;
    }

    public function setRequisitionIndex(?int $requisition_index): self
    {
        $this->requisition_index = $requisition_index;

        return $this;
    }
}
