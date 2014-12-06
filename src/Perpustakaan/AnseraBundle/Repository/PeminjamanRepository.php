<?php

namespace Perpustakaan\AnseraBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Perpustakaan\AnseraBundle\Entity\Peminjaman;


/**
 * Repository for handling JobProfile entities
 *
 */

class PeminjamanRepository extends EntityRepository
{

  public function getTotalPinjamBuku($idAnggota){
       $rsm = new ResultSetMapping();
       $rsm->addScalarResult('total', 'total');
      
       $sql="SELECT count(A.id)as total FROM trx_detail_peminjaman A
          INNER JOIN trx_peminjaman B ON A.peminjaman_id=B.id
          INNER JOIN anggota C ON C.id=B.anggota_id
          WHERE A.tanggal_kembali IS NULL AND anggota_id=?;
          ";
       $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);

       $query->setParameter(1, $idAnggota);

       $result=$query->getResult();
       return $result ;     
  
  }

  
  public function getBukuPinjaman($idPeminjaman){

     $rsm = new ResultSetMapping();
       $rsm->addScalarResult('id_peminjaman', 'id_peminjaman');
       $rsm->addScalarResult('tanggal_pinjam', 'tanggal_pinjam');
       $rsm->addScalarResult('id_detail_peminjaman', 'id_detail_peminjaman');
       $rsm->addScalarResult('judul', 'judul');
       $rsm->addScalarResult('tanggal_pinjam', 'tanggal_pinjam');
       $rsm->addScalarResult('tahun_terbit', 'tahun_terbit');
       $rsm->addScalarResult('nama', 'nama');
       $rsm->addScalarResult('tanggal_bergabung', 'tanggal_bergabung');
       $rsm->addScalarResult('no_telepon', 'no_telepon');
       $rsm->addScalarResult('alamat', 'alamat');
       $rsm->addScalarResult('denda', 'denda');
       $rsm->addScalarResult('penulis', 'penulis');
       $rsm->addScalarResult('tanggal_kembali', 'tanggal_kembali');
       $rsm->addScalarResult('id', 'id');

       $sql="SELECT C.id,B.id as id_peminjaman,A.tanggal_kembali,B.tanggal_pinjam,A.denda,
            A.id as id_detail_peminjaman,C.judul,
            C.penulis,C.penerbit,C.isbn,C.tahun_terbit,
            D.nama,D.tanggal_bergabung,D.no_telepon,D.alamat
            FROM trx_detail_peminjaman A
            INNER JOIN trx_peminjaman B ON A.peminjaman_id=B.id 
            INNER JOIN buku C ON C.id=A.buku_id
            INNER JOIN anggota D ON D.id=B.anggota_id WHERE A.tanggal_kembali IS NULL AND B.id=?";

       $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);

       $query->setParameter(1, $idPeminjaman);

       $result=$query->getResult();
       return $result ;     
  }

  
  public function getLaporan($tglAwal,$tglAkhir){
       $rsm = new ResultSetMapping();
       $rsm->addScalarResult('id_peminjaman', 'id_peminjaman');
       $rsm->addScalarResult('tanggal_pinjam', 'tanggal_pinjam');
       $rsm->addScalarResult('id_detail_peminjaman', 'id_detail_peminjaman');
       $rsm->addScalarResult('judul', 'judul');
       $rsm->addScalarResult('tanggal_pinjam', 'tanggal_pinjam');
       $rsm->addScalarResult('tahun_terbit', 'tahun_terbit');
       $rsm->addScalarResult('nama', 'nama');
       $rsm->addScalarResult('tanggal_bergabung', 'tanggal_bergabung');
       $rsm->addScalarResult('no_telepon', 'no_telepon');
       $rsm->addScalarResult('alamat', 'alamat');
       $rsm->addScalarResult('penerbit', 'penerbit');

       $rsm->addScalarResult('denda', 'denda');
       $rsm->addScalarResult('penulis', 'penulis');
       $rsm->addScalarResult('tanggal_kembali', 'tanggal_kembali');
       $rsm->addScalarResult('id', 'id');

       $sql="SELECT C.id,B.id as id_peminjaman,A.tanggal_kembali,B.tanggal_pinjam,A.denda,
            A.id as id_detail_peminjaman,C.judul,
            C.penulis,C.penerbit,C.isbn,C.tahun_terbit,
            D.nama,D.tanggal_bergabung,D.no_telepon,D.alamat
            FROM trx_detail_peminjaman A
            INNER JOIN trx_peminjaman B ON A.peminjaman_id=B.id 
            INNER JOIN buku C ON C.id=A.buku_id
            INNER JOIN anggota D ON D.id=B.anggota_id WHERE A.tanggal_kembali IS NULL AND B.tanggal_pinjam BETWEEN '".$tglAwal."' AND '".$tglAkhir."'";

       $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);

       $result=$query->getResult();
       return $result ;     
  }

	public function getAll($idAnggota="",$idPeminjaman=""){
  
    if(!empty($idAnggota)){
        $where=" WHERE D.id=?";
    }elseif(!empty($idPeminjaman)){
        $where=" WHERE B.id=?";
    }else{
        $where="";
    }
    
	   $rsm = new ResultSetMapping();
       $rsm->addScalarResult('id_peminjaman', 'id_peminjaman');
       $rsm->addScalarResult('tanggal_pinjam', 'tanggal_pinjam');
       $rsm->addScalarResult('id_detail_peminjaman', 'id_detail_peminjaman');
       $rsm->addScalarResult('judul', 'judul');
       $rsm->addScalarResult('tanggal_pinjam', 'tanggal_pinjam');
       $rsm->addScalarResult('tahun_terbit', 'tahun_terbit');
       $rsm->addScalarResult('nama', 'nama');
       $rsm->addScalarResult('tanggal_bergabung', 'tanggal_bergabung');
       $rsm->addScalarResult('no_telepon', 'no_telepon');
       $rsm->addScalarResult('alamat', 'alamat');
       $rsm->addScalarResult('denda', 'denda');
       $rsm->addScalarResult('penulis', 'penulis');
       $rsm->addScalarResult('tanggal_kembali', 'tanggal_kembali');
       $rsm->addScalarResult('id', 'id');

       $sql="SELECT C.id,B.id as id_peminjaman,A.tanggal_kembali,B.tanggal_pinjam,A.denda,A.id as id_detail_peminjaman,C.judul,
			C.penulis,C.penerbit,C.isbn,C.tahun_terbit,
			D.nama,D.tanggal_bergabung,D.no_telepon,D.alamat
			FROM trx_detail_peminjaman A
			INNER JOIN trx_peminjaman B ON A.peminjaman_id=B.id 
			INNER JOIN buku C ON C.id=A.buku_id
			INNER JOIN anggota D ON D.id=B.anggota_id ".$where.
      " ORDER BY id_detail_peminjaman DESC ";

  	   $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);

   	   if(!empty($idAnggota)){
         $query->setParameter(1, $idAnggota);
	     }elseif(!empty($idPeminjaman)){
           $query->setParameter(1, $idPeminjaman);
       }

       $result=$query->getResult();
   	   return $result ;		  
	}

	public function checkDetailPeminjaman($idBuku,$idAnggota){

	   $rsm = new ResultSetMapping();
       $rsm->addScalarResult('total', 'total');

       $sql="SELECT COUNT(*) as total FROM trx_detail_peminjaman A 
       	     INNER JOIN trx_peminjaman B ON A.peminjaman_id=B.id
       	     WHERE A.buku_id=? AND B.anggota_id=? AND tanggal_kembali IS NULL
       		";

  	   $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);

       $query->setParameter(1, $idBuku);
       $query->setParameter(2, $idAnggota);

       $result=$query->getResult();
   	   return $result[0]['total'] ;		  
		
	}
}