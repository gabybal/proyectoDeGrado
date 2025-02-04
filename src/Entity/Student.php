<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string")]
    #[Assert\Length(
        min: 10,
        max: 10,
        exactMessage: "La cédula debe tener exactamente 10 dígitos."
    )]
    #[Assert\Regex(
        pattern: "/^\d{10}$/",
        message: "La cédula solo debe contener números y debe ser de 10 dígitos."
    )]
    private ?string $cedula = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, Prestamo>
     */
    #[ORM\OneToMany(targetEntity: Prestamo::class, mappedBy: 'student')]
    private Collection $prestamos;

    public function __construct()
    {
        $this->prestamos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCedula(): ?string
    {
        return $this->cedula;
    }

    public function setCedula(string $cedula): static
    {
        $this->cedula = $cedula;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, Prestamo>
     */
    public function getPrestamos(): Collection
    {
        return $this->prestamos;
    }

    public function addPrestamo(Prestamo $prestamo): static
    {
        if (!$this->prestamos->contains($prestamo)) {
            $this->prestamos->add($prestamo);
            $prestamo->setStudent($this);
        }

        return $this;
    }

    public function removePrestamo(Prestamo $prestamo): static
    {
        if ($this->prestamos->removeElement($prestamo)) {
             
            if ($prestamo->getStudent() === $this) {
                $prestamo->setStudent(null);
            }
        }

        return $this;
    }
}
