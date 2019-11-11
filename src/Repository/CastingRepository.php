<?php

namespace App\Repository;

use App\Entity\Casting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Casting::class);
    }

    /**
     * EXO 2 : récupérer les moviecasts d'un movie donné + les infos de Person
     * Méthode DQL
     *
     * @param Movie $movie
     * @return Casting[]
     */
    public function findByMovieDQL($movie)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT c, p 
            FROM App\Entity\Casting c
            JOIN c.person p
            WHERE c.movie = :movie
        ')
        ->setParameter('movie', $movie);

        return $query->getResult();
    }

    public function findByMovieDQLForSerializing($movie)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT c.role, p.name 
            FROM App\Entity\Casting c
            JOIN c.person p
            WHERE c.movie = :movie
        ')
        ->setParameter('movie', $movie);

        return $query->getResult();
    }

    /**
     * EXO 2 : récupérer les moviecasts d'un movie donné + les infos de Person
     * Méthode Query Builder
     *
     * @param Movie $movie
     * @return Casting[]
     */
    public function findByMovieQueryBuilder($movie)
    {
        $qb = $this->createQueryBuilder('c')
           ->join('c.person', 'p')
           ->addSelect('p')
           ->where('c.movie = :myMovie')
           ->setParameter('myMovie', $movie)
       ;
       
        //cast retour de requete

        return $qb->getQuery()->getArrayResult();
    }
}
