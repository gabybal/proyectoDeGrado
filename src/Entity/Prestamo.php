<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PrestamoRepository;


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

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaPrestamo = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaDevolucion = null;

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

    public function getFechaPrestamo(): ?\DateTimeInterface
    {
        return $this->fechaPrestamo;
    }

    public function setFechaPrestamo(\DateTimeInterface $fechaPrestamo): static
    {
        $this->fechaPrestamo = $fechaPrestamo;

        return $this;
    }

    public function getFechaDevolucion(): ?\DateTimeInterface
    {
        return $this->fechaDevolucion;
    }

    public function setFechaDevolucion(?\DateTimeInterface $fechaDevolucion): static
    {
        $this->fechaDevolucion = $fechaDevolucion;
        
        return $this;
    }

    // Método para determinar si el libro está prestado
    public function estaPrestado(): bool
    {
        // Si no tiene fecha de devolución, se considera prestado
        return $this->fechaDevolucion === null;
    }
}
