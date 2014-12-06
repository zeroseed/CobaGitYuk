<?php

namespace Perpustakaan\AnseraBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Perpustakaan\AnseraBundle\Entity\Kategori;


/**
 * Repository for handling JobProfile entities
 *
 */

class KategoriRepository extends EntityRepository
{

	public function getQueryKategori(){
	   $rsm = new ResultSetMapping();
       $rsm->addScalarResult('no_rak', 'no_rak');
       $rsm->addScalarResult('nama_kategori', 'nama_kategori');
	
  	   $sql="SELECT no_rak,nama_kategori FROM kategori";

	   $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);
	   $result=$query->getResult();
	   	   
   	   return $result ;		  
	}
			
}