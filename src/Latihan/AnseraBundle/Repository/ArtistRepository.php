<?php

namespace Latihan\AnseraBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Latihan\AnseraBundle\Entity\Artist;


class ArtistRepository extends EntityRepository
{

	public function getQueryArtist(){
	   $rsm = new ResultSetMapping();
       $rsm->addScalarResult('nama', 'nama');
       $rsm->addScalarResult('pekerjaan', 'pekerjaan');
	
  	   $sql="SELECT nama,pekerjaan FROM artist";

	   $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);
	   $result=$query->getResult();
	   	   
   	   return $result ;		  
	}
			
}