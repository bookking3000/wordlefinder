<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestRepository::class)
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $word_expression;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mustHaveChars;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $forbiddenChars;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $indexedForbiddenQueryExpression = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWordExpression(): ?string
    {
        return $this->word_expression;
    }

    public function setWordExpression(string $word_expression): self
    {
        $this->word_expression = $word_expression;

        return $this;
    }

    public function getMustHaveChars(): ?string
    {
        return $this->mustHaveChars;
    }

    public function setMustHaveChars(?string $mustHaveChars): self
    {
        $this->mustHaveChars = $mustHaveChars;

        return $this;
    }

    public function getForbiddenChars(): ?string
    {
        return $this->forbiddenChars;
    }

    public function setForbiddenChars(?string $forbiddenChars): self
    {
        $this->forbiddenChars = $forbiddenChars;

        return $this;
    }

    public function getIndexedForbiddenQueryExpression(): ?array
    {
        return $this->indexedForbiddenQueryExpression;
    }

    public function setIndexedForbiddenQueryExpression(?array $indexedForbiddenQueryExpression): self
    {
        $this->indexedForbiddenQueryExpression = $indexedForbiddenQueryExpression;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
