<?php

namespace UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\Pub;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PubRepository extends EntityRepository
{
	public function getPubs($first_result, $max_results=10)
	{
		$qb=$this->createQueryBuilder('pub');
		$qb->select('pub')->setFirstResult($first_result)->setMaxResults($max_results);
		$qb->addSelect('categorie')->leftJoin('pub.categorie','categorie');
		$qb->addSelect('user')->leftJoin('pub.user','user');
		$qb->addSelect('file')->leftJoin('pub.files','file');
		$qb->orderBy('pub.dateheure DESC, pub.titre');
		//$qb->addOrderBy('pub.dateheure', 'DESC');
		
		$pag=new Paginator($qb);
		return $pag;
		//count($pag) = nombre de pub dans la bd
	}

    public function searchPubs($text, $first_result, $max_results=10)
    {
        $qb=$this->createQueryBuilder('pub');
        $qb->select('pub')->setFirstResult($first_result)->setMaxResults($max_results);
        $qb->addSelect('categorie')->leftJoin('pub.categorie','categorie');
        $qb->addSelect('user')->leftJoin('pub.user','user');
        $qb->addSelect('file')->leftJoin('pub.files','file');

        $qb->where($qb->expr()->orX(
            $qb->expr()->like('LOWER(pub.titre)','?1'),
            $qb->expr()->like('LOWER(pub.contenu)','?2')
        ) );
        $qb->orderBy('pub.dateheure DESC, pub.titre');
        //$qb->addOrderBy('pub.dateheure', 'DESC');

        $value='%'.strtolower($text).'%';
        $qb->setParameter(1,$value);
        $qb->setParameter(2,$value);
        $pag=new Paginator($qb);
        return $pag;
        //count($pag) = nombre de pub dans la bd
    }
}
