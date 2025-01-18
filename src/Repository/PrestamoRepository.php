<?php

namespace App\Repository;

use App\Entity\Prestamo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prestamo>
 */
class PrestamoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prestamo::class);
    }

    // Método para verificar si ya existe un préstamo activo para un libro y estudiante
    public function findActiveLoanByStudentAndBook($studentId, $bookId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.student = :studentId')
            ->andWhere('p.book = :bookId')
            ->andWhere('p.fechaDevolucion IS NULL')  // Verificamos que no tenga fecha de devolución
            ->setParameter('studentId', $studentId)
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Obtener préstamos activos (sin fecha de devolución)
    public function findActiveLoans(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.fechaDevolucion IS NULL')
            ->getQuery()
            ->getResult();
    }

    // Obtener historial de préstamos de un estudiante
    public function findLoansByStudent($studentId): array
    {
        return $this->createQueryBuilder('p')
          ->andWhere('p.student = :studentId')
          ->setParameter('studentId', $studentId)
          ->orderBy('p.fechaPrestamo', 'DESC')
          ->getQuery()
          ->getResult();
    }

    // Obtener estadísticas de los libros más prestados
    public function findMostLoanedBooks(): array
    {
        return $this->createQueryBuilder('p')
            ->select('b.title, COUNT(p.id) AS loan_count')
            ->join('p.book', 'b')
            ->groupBy('b.id')
            ->orderBy('loan_count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

