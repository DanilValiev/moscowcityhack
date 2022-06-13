<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

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
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkExist(int $externalId): bool
    {
        return $this->findOneBy(['externalId' => $externalId]) != NULL;
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
            ->orWhere("p.tnved = '{$data}'")
            ->orWhere("p.gost = '{$data}'")
            ->addOrderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByfilters(?string $odkp, ?string $ogrn): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Product::class, 'le');

        if ($odkp != NUll && $odkp != 0) {
            $where = "o.code = {$odkp}";
        } else {
            $where = 'true';
        }

        if ($ogrn != NULL && $ogrn != 0) {
            $andWhere = "pd.ogrn = '{$ogrn}'";
        } else {
            $andWhere = 'true';
        }

        return $this->_em->createNativeQuery("
            SELECT 
                   p.*
            FROM 
                 product as p
            JOIN 
                     odkv o on p.odkp2 = o.code
            JOIN     
                    production pd on pd.id = p.company_id 
            WHERE 
                  {$where}
            AND 
                  {$andWhere}
        ", $rsm)
            ->getArrayResult();
    }

    public function findProductPaginated(int $page, int $peerPage): PaginationInterface
    {
        $query = $this->createQueryBuilder('p')
            ->addOrderBy('p.id', 'ASC')
            ->getQuery();

        return $this->paginator->paginate($query, $page, $peerPage);
    }

    public function detach(Product $product)
    {
        $this->_em->detach($product);
    }

    public function flush()
    {
        $this->_em->flush();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
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

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
