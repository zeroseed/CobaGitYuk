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

/**
 * @DI\Service("perpus.service.peminjaman")
 * @DI\Tag("security.secure_service")
 */
class PeminjamanService
{

    protected $entityManager;

    protected $passwordEncoder;

    protected $session;

    /**
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *       "passwordEncoder" = @DI\Inject("security.encoder.blowfish"),
   	 * 	   "session" = @DI\Inject("session"),
	 *  })
     */
    public function __construct(EntityManager $entityManager,BasePasswordEncoder $passwordEncoder,$session) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session=$session;

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

    public function getDetailPeminjaman($idAnggota="",$idPeminjaman=""){
        $detailPeminjaman=$this->_getPeminjamanRepository()->getAll($idAnggota,$idPeminjaman);
       
        return $detailPeminjaman;
    }

    public function getBukuPinjaman($id_peminjaman){
        return $this->_getPeminjamanRepository()->getBukuPinjaman($id_peminjaman);
    }
    public function getTotalPinjamBuku($idAnggota){
        $getTotalPinjamBuku=$this->_getPeminjamanRepository()->getTotalPinjamBuku($idAnggota);
        return $getTotalPinjamBuku;
    
    }

    public function getLaporan($parameters){
        $tgl1=date("Y-m-d",strtotime($parameters["tglAwal"]));
        $tgl2=date("Y-m-d",strtotime($parameters["tglAkhir"]));

        $laporan=$this->_getPeminjamanRepository()->getLaporan($tgl1,$tgl2);

        return $laporan;
    }

    public function deleteDetailPeminjaman($id){
        
        $detailPeminjaman=$this->_getDetailPeminjamanRepository()->find($id);
        if(!is_null($detailPeminjaman) && !empty($detailPeminjaman)){

            $this->entityManager->remove($detailPeminjaman);
            $this->entityManager->flush();
        }
        return true;
    }
    public function savePeminjaman($data){
        $idBuku=$data["idBuku"];
        $idAnggota=$data["idAnggota"];
        $idPetugas=$data["idPetugas"];
        $tanggal_pinjam=new \DateTime("now");

        $buku=$this->_getBukuRepository()->find($idBuku);
        
        $totalKeluar=$buku->getTotalKeluar()+1;
        $totalTersedia=$buku->getTotal()-$totalKeluar;

        $buku->setTotalKeluar($totalKeluar);
        $buku->setTotalTersedia($totalTersedia);

        
        $anggota=$this->_getAnggotaRepository()->find($idAnggota);
        
        $petugas=$this->_getPetugasRepository()->find($idPetugas);

        $detailPeminjaman=$this->_getPeminjamanRepository()->checkDetailPeminjaman($idBuku,$idAnggota);
        
        //check detail peminjaman

        if($detailPeminjaman > 0){
            return array("level"=>"error","errMessage"=>"Buku tersebut sudah dipinjam");
        }else{
			$peminjaman=$this->_getPeminjamanRepository()->findOneBy(array("anggota"=>$idAnggota,"tanggal_pinjam"=>$tanggal_pinjam));		
		    if(!empty($peminjaman) && !is_null($peminjaman)){
            	$this->session->set("id_peminjaman",$peminjaman->getId());
	        }else{
	            $newPeminjaman=new Peminjaman();
	            $newPeminjaman->setAnggota($anggota);
	            $newPeminjaman->setPetugas($petugas);
	            $newPeminjaman->setTanggalPinjam();
	            $this->entityManager->persist($newPeminjaman);
	            $this->entityManager->flush();
	            $peminjaman=$newPeminjaman;
	        }
			
		    $saveDetailPeminjaman=$this->saveDetailPeminjaman($buku,$peminjaman);
            return array("level"=>"success");
        }
    }

    public function saveDetailPeminjaman(Buku $buku, Peminjaman $peminjaman){
        $newDetailPeminjaman=new DetailPeminjaman();
        $newDetailPeminjaman->setBuku($buku);
        $newDetailPeminjaman->setPeminjaman($peminjaman);
        $this->entityManager->persist($newDetailPeminjaman);
        $this->entityManager->flush();

        return array("level"=>"success");
    }
    public function getPeminjaman($id_peminjaman=""){
        if(!empty($id_peminjaman)){
            $peminjaman=$this->_getPeminjamanRepository()->find($id_peminjaman);
        }else{
            $peminjaman=$this->_getPeminjamanRepository()->findBy(array(),array('id'=>'DESC'));     
        }
        return $peminjaman;
    }


}
