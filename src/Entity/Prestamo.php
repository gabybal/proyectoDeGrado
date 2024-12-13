<?php

namespace App\Entity;

use App\Repository\PrestamoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestamoRepository::class)]
class Prestamo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prestamos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'prestamos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column]
    private ?int $fechaPrestamo = null;

    #[ORM\Column(nullable: true)]
    private ?int $fechaDevolucion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getFechaPrestamo(): ?int
    {
        return $this->fechaPrestamo;
    }

    public function setFechaPrestamo(int $fechaPrestamo): static
    {
        $this->fechaPrestamo = $fechaPrestamo;

        return $this;
    }

    public function getFechaDevolucion(): ?int
    {
        return $this->fechaDevolucion;
    }

    public function setFechaDevolucion(?int $fechaDevolucion): static
    {
        $this->fechaDevolucion = $fechaDevolucion;

        return $this;
    }
}
