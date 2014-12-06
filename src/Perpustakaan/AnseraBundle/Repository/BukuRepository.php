<?php

namespace Perpustakaan\AnseraBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Perpustakaan\AnseraBundle\Entity\Buku;

class BukuRepository extends EntityRepository
{
	public function getTersedia(){
	   $rsm = new ResultSetMapping();
       $rsm->addScalarResult('id', 'id');
       $rsm->addScalarResult('judul', 'judul');
       $rsm->addScalarResult('penulis', 'penulis');
       $rsm->addScalarResult('penerbit', 'penerbit');
       $rsm->addScalarResult('tahun_terbit', 'tahun_terbit');
       $rsm->addScalarResult('isbn', 'isbn');
       $rsm->addScalarResult('kategori', 'nama_kategori');
	
  	   $sql="SELECT A.id,judul,penulis,penerbit,tahun_terbit,isbn,nama_kategori  
  	   		 FROM buku A
  	   		 INNER JOIN kategori B ON B.id=A.kategori_id 
  	   		 WHERE A.total_tersedia > 0 
  	   		";

	   $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);
	   $result=$query->getResult();
	   	   
   	   return $result ;		  
	}
			
	public function getTotalJenisBuku(){

	   $rsm = new ResultSetMapping();
       $rsm->addScalarResult('total', 'total');
	
	   $sql="SELECT COUNT(*) as total FROM buku WHERE total_tersedia > 0 ";
	   $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);
	   $result=$query->getResult();
	   	   
   	   return $result[0]['total'] ;		  
		
	}		
}