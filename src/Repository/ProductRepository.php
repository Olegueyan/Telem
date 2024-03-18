<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var QueryBuilder queryBuilder
     */
    private QueryBuilder $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // ***************************************************************
    // Region **H1** Méthodes retournant un QueryBuilder
    // ***************************************************************

    // ---------------------------------------------------------------
    // Region --H2-- Initialisation du QueryBuilder
    // ---------------------------------------------------------------

    private function initializeQueryBuilder(): void
    {
        $this->qb = $this->createQueryBuilder('p')->select('p');
    }

    private function initializeQueryBuilderWithCount(): void
    {
        $this->qb = $this->createQueryBuilder('p')->select('count(p.id)');
    }

    // ---------------------------------------------------------------
    // Region --H2-- Initialisation du QueryBuilder
    // ---------------------------------------------------------------

    // ---------------------------------------------------------------
    // Region --H2-- Filtres
    // ---------------------------------------------------------------

    private function orPropertyLike(string $property, string $keywords): void
    {
        $this->qb
            ->orWhere('p.'.$property.' LIKE :'.$property)
            ->setParameter($property, '%'.$keywords.'%');
    }

    // ---------------------------------------------------------------
    // Region --H2-- Filtres
    // ---------------------------------------------------------------

    // ---------------------------------------------------------------
    // Region --H2-- QueryBuilder mobilisant des filtres et/ou des jointures
    // ---------------------------------------------------------------

    public function searchQb(string $keywords): void
    {
        $this->orPropertyLike('name', $keywords);
        $this->orPropertyLike('description', $keywords);
    }

    // ---------------------------------------------------------------
    // Region --H2-- QueryBuilder mobilisant des filtres et/ou des jointures
    // ---------------------------------------------------------------

    // ***************************************************************
    // Region **H1** Méthodes retournant un QueryBuilder
    // ***************************************************************

    // ***************************************************************
    // Region **H1** Méthodes retournant un jeu de résultats
    // ***************************************************************

    public function search(string $keywords): array
    {
        $this->initializeQueryBuilder();
        $this->searchQb($keywords);
        return $this->qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function searchCount(string $keywords): int
    {
        $this->initializeQueryBuilderWithCount();
        $this->searchQb($keywords);
        return $this->qb->getQuery()->getSingleScalarResult();
    }

    // ***************************************************************
    // Region **H1** Méthodes retournant un jeu de résultats
    // ***************************************************************
}
