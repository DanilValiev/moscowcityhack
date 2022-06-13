<?php

namespace App\Repository;

use App\Entity\Production;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Production>
 *
 * @method Production|null find($id, $lockMode = null, $lockVersion = null)
 * @method Production|null findOneBy(array $criteria, array $orderBy = null)
 * @method Production[]    findAll()
 * @method Production[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, Production::class);
    }

    public function add(Production $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Production $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkExist(string $ogrn): bool
    {
        return $this->findOneBy(['Ogrn' => $ogrn]) != NULL;
    }

    public function findAllIterable(int $offset, int $limit): iterable
    {
        return $this->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->addOrderBy('p.id', 'ASC')
            ->getQuery()
            ->toIterable()
        ;
    }

    public function findProductionByName(string $data)
    {
        return $this->createQueryBuilder('p')
            ->where("p.Title LIKE '%{$data}%'")
            ->orWhere("p.inn = '{$data}'")
            ->orWhere("p.Ogrn = '{$data}'")
            ->orWhere("p.Address LIKE '%{$data}%'")
            ->addOrderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findProductionsPaginated(int $page, int $peerPage): PaginationInterface
    {
        $query = $this->createQueryBuilder('p')
            ->addOrderBy('p.id', 'ASC')
            ->getQuery();

        return $this->paginator->paginate($query, $page, $peerPage);
    }

    public function findByfilters(?string $odkp, ?string $capital): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Production::class, 'le');

        if ($odkp != NUll && $odkp != 0) {
            $where = "o.code = {$odkp}";
        } else {
            $where = 'true';
        }

        return $this->_em->createNativeQuery("
            SELECT 
                   p.title, p.id, p.staturory_capital, p.title, p.address, p.ogrn, p.inn, p.url
            FROM 
                 production as p
            JOIN 
                     odkv o on p.odvk_primary_id = o.id
            WHERE 
                  {$where}
            AND 
                  p.staturory_capital > :capital
        ", $rsm)
            ->setParameters([
                'capital' => $capital
            ])
            ->getArrayResult();
    }

    public function detach(Production $product)
    {
        $this->_em->detach($product);
    }

    public function flush()
    {
        $this->_em->flush();
    }
//    /**
//     * @return Production[] Returns an array of Production objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Production
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
