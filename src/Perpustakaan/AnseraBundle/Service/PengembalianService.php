<?php

namespace Perpustakaan\AnseraBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Monolog\Logger;

use Perpustakaan\AnseraBundle\Entity\Kategori;
use Perpustakaan\AnseraBundle\Entity\Buku;
use Perpustakaan\AnseraBundle\Entity\Peminjaman;
use Perpustakaan\AnseraBundle\Entity\DetailPeminjaman;
use Perpustakaan\AnseraBundle\Entity\Petugas;
use Perpustakaan\AnseraBundle\Entity\Anggota;
use Perpustakaan\AnseraBundle\Entity\Pengembalian;
use Perpustakaan\AnseraBundle\Entity\DetailPengembalian;


/**
 * @DI\Service("perpus.service.pengembalian")
 * @DI\Tag("security.secure_service")
 */
class PengembalianService
{

    protected $entityManager;

    protected $passwordEncoder;

    protected $session;
    protected $peminjamanService;


    /**
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *       "passwordEncoder" = @DI\Inject("security.encoder.blowfish"),
   	 * 	   "session" = @DI\Inject("session"),
     *     "peminjamanService" = @DI\Inject("perpus.service.peminjaman"),
	 *  })
     */
    public function __construct(EntityManager $entityManager,BasePasswordEncoder $passwordEncoder,$session,PeminjamanService $peminjamanService) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session=$session;
        $this->peminjamanService=$peminjamanService;

    }

    protected function _getPeminjamanRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Peminjaman');
    }

    protected function _getDetailPeminjamanRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:DetailPeminjaman');
    }

    protected function _getAnggotaRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Anggota');
    }

    protected function _getPetugasRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Petugas');
    }

    protected function _getBukuRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Buku');
    }

    protected function _getDetailPengembalianRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:DetailPengembalian');
    }

    protected function _getPengembalianRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Pengembalian');
    }

    public function savePengembalian($data){
        $idPeminjaman=$data["id_peminjaman"];
        $idBuku=$data["idBuku"];
        $idPetugas=$data["idPetugas"];
        $idAnggota=$data["idAnggota"];
        $status=$data["status"];
        $peminjaman=$this->_getPeminjamanRepository()->find($idPeminjaman);
        $anggota=$this->_getAnggotaRepository()->find($idAnggota);
        $petugas=$this->_getPetugasRepository()->find($idPetugas);

        $buku=$this->_getBukuRepository()->find($idBuku);
        $tgl_kembali=new \DateTime("now");
        $pengembalian=$this->_getPengembalianRepository()->findOneBy(array("peminjaman"=>$idPeminjaman));
        
        if(empty($pengembalian) || is_null($pengembalian)){
            $newPengembalian=new Pengembalian();
            $newPengembalian->setAnggota($anggota);
            $newPengembalian->setPeminjaman($peminjaman);
            $newPengembalian->setPetugas($petugas);
            $this->entityManager->persist($newPengembalian);
            $pengembalian=$newPengembalian;
            $this->entityManager->flush();
        }

        //save detail pengembalian
        $newDetailPengembalian=new DetailPengembalian();
        $newDetailPengembalian->setPengembalian($pengembalian);
        $newDetailPengembalian->setBuku($buku);
        $newDetailPengembalian->setPeminjaman($peminjaman);
        $newDetailPengembalian->setTanggal();
        $newDetailPengembalian->setStatus($status);
        $this->entityManager->persist($newDetailPengembalian);

        if($status=="rusak/hilang"){
            $this->hilangRusak($buku,$peminjaman,$pengembalian,"hilang/rusak");
        }elseif($status=="mengembalikan"){
            $this->mengembalikanBuku($buku,$peminjaman,$pengembalian,"mengembalikan");
        }elseif($status=="memperpanjang"){
            $this->memperpanjang($buku,$data);
        }

        $this->updateDetailPeminjaman($idPeminjaman,$idBuku);
        return array("level"=>"success");
    }
    public function getLaporan($parameters){
        $tgl1=$parameters["tglAwal"];
        $tgl2=$parameters["tglAkhir"];
        $laporan=$this->_getPengembalianRepository()->getLaporan($tgl1,$tgl2);
        return $laporan;
    }

    public function getSelisihTanggal($tgl1,$tgl2){

        $pecah1 = explode("-", $tgl1);
        $date1 = $pecah1[2];
        $month1 = $pecah1[1];
        $year1 = $pecah1[0];

        $pecah2 = explode("-", $tgl2);
        $date2 = $pecah2[2];
        $month2 = $pecah2[1];
        $year2 =  $pecah2[0];

        // menghitung JDN dari masing-masing tanggal

        $jd1 = GregorianToJD($month1, $date1, $year1);
        $jd2 = GregorianToJD($month2, $date2, $year2);

        // hitung selisih hari kedua tanggal

        $selisih = $jd2 - $jd1;
        return $selisih;
    
    }

    public function updateDetailPeminjaman($idPeminjaman,$idBuku){
        $detailPeminjaman=$this->_getDetailPeminjamanRepository()->findOneBy(array("peminjaman"=>$idPeminjaman,"buku"=>$idBuku));
        if(!empty($detailPeminjaman) && !is_null($detailPeminjaman)){
            $dendaPerHari=500;

            $tglPinjam=$detailPeminjaman->getPeminjaman()->getTanggalPinjam()->format("Y-m-d");
            $tglKembali=date("Y-m-d",strtotime("+7 days",strtotime($tglPinjam)));
            $sekarang=date("Y-m-d");
            $terlambat=$this->getSelisihTanggal($tglKembali,$sekarang);
            $denda=0;

            if($terlambat > 0 ){
                $denda=$dendaPerHari * $terlambat;
                $detailPeminjaman->setTerlambat($terlambat);
            }else{
                $detailPeminjaman->setTerlambat(0);                
            }

            $detailPeminjaman->setDenda($denda);
            $detailPeminjaman->setTanggalKembali($sekarang);
            $this->entityManager->flush();
        }        
    }

    public function memperpanjang(Buku $buku,$data){
        $total=$buku->getTotal();
        $totalKeluar=$buku->getTotalKeluar()-1;
        $totalTersedia=$total-$totalKeluar;
               
        $buku->setTotalTersedia($totalTersedia);
        $buku->setTotalKeluar($totalKeluar);
        $this->entityManager->flush();

        $this->peminjamanService->savePeminjaman($data);
        return array("level"=>"success");
    }

    public function mengembalikanBuku(Buku $buku){
        //update data buku
        $total=$buku->getTotal();
        $totalKeluar=$buku->getTotalKeluar()-1;
        $totalTersedia=$total-$totalKeluar;
               
        $buku->setTotalTersedia($totalTersedia);
        $buku->setTotalKeluar($totalKeluar);
        $this->entityManager->flush();


        return array("level"=>"success");
    }

    public function hilangRusak(Buku $buku){

        //update data buku
        $total=$buku->getTotal()-1;
        $totalKeluar=$buku->getTotalKeluar()-1;
        $totalTersedia=$total-$totalKeluar;
        $totalHilangRusak=$buku->getRusakHilang() + 1;

        $buku->setTotal($total);
        $buku->setTotalTersedia($totalTersedia);
        $buku->setTotalKeluar($totalKeluar);
        $buku->setRusakHilang($totalHilangRusak);
        $this->entityManager->flush();

        return array("level"=>"success");
    }


}
