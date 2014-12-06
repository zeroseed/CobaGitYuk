<?php

namespace Perpustakaan\AnseraBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

use Perpustakaan\AnseraBundle\Entity\Peminjaman;


/**
 * Repository for handling JobProfile entities
 *
 */

class PengembalianRepository extends EntityRepository
{

  public function getLaporan($tglAwal,$tglAkhir){
     $rsm = new ResultSetMapping();
       $rsm->addScalarResult('tanggal_pinjam', 'tanggal_pinjam');
       $rsm->addScalarResult('judul', 'judul');
       $rsm->addScalarResult('tahun_terbit', 'tahun_terbit');
       $rsm->addScalarResult('nama', 'nama');
       $rsm->addScalarResult('penerbit', 'penerbit');
       $rsm->addScalarResult('terlambat', 'terlambat');

       $rsm->addScalarResult('denda', 'denda');
       $rsm->addScalarResult('penulis', 'penulis');
       $rsm->addScalarResult('tanggal_kembali', 'tanggal_kembali');
       $rsm->addScalarResult('id', 'id');

       $sql="SELECT E.tanggal_pinjam,D.nama,A.tanggal as tanggal_kembali,tahun_terbit,penerbit,judul,penulis,F.denda,F.terlambat FROM trx_detail_pengembalian A 
            INNER JOIN trx_pengembalian B ON A.pengembalian_id=B.id
            INNER JOIN buku C ON C.id=A.buku_id
            INNER JOIN anggota D ON D.id=B.anggota_id
            INNER JOIN trx_peminjaman E ON E.id=A.peminjaman_id
            INNER JOIN trx_detail_peminjaman F ON F.peminjaman_id=B.peminjaman_id AND E.id=F.peminjaman_id
            WHERE F.tanggal_kembali IS NOT NULL
            ";
       $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);

       $result=$query->getResult();
       return $result ;     
  }
}
?>