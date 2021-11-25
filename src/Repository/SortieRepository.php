<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSortieByCampus($campus){

        $queryBuilder = $this->createQueryBuilder('s');

        if($campus){
            $queryBuilder
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus);
        }
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function search ($mots = null,
                            $campus=null,
                            $date1= null,
                            $date2 = null,
                            $jeSuisOrganisateur=null,
                            $jeSuisInscrit=null,
                            $jeSuisPasInscrit=null,
                            $sortiePasse=null,
                            $user=null
    ){
        $querybuilder = $this->createQueryBuilder('s');
        if ($mots != null){
            $querybuilder->where('s.nom LIKE :mots ')
                ->setParameter('mots','%'. $mots.'%');
        }
        if ($campus != null){
            $querybuilder->andWhere(' s.campus = :campus ')
                ->setParameter('campus', $campus);
        }
        if ($date1 != null && $date2 != null){
            $querybuilder->andWhere('s.dateHeureDebut BETWEEN :date1  and :date2')
                ->setParameter('date1', $date1->format('Y-m-d'))
                ->setParameter('date2', $date2->format('Y-m-d'));
        }
        if ($jeSuisOrganisateur != null){

            $querybuilder->andWhere('s.organisateur = :user')
                ->setParameter(':user', $user);
        }

        $query = $querybuilder->getQuery();
        return $query->getResult();
    }


    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
