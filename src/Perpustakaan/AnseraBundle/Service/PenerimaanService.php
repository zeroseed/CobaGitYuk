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
use Perpustakaan\AnseraBundle\Entity\Penerimaan;
use Perpustakaan\AnseraBundle\Entity\Petugas;
use Perpustakaan\AnseraBundle\Entity\DetailPenerimaan;

/**
 * @DI\Service("perpus.service.penerimaan")
 * @DI\Tag("security.secure_service")
 */
class PenerimaanService
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

    protected function _getKategoriRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Kategori');
    }

    protected function _getBukuRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Buku');
    }

    protected function _getDetailPenerimaanRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:DetailPenerimaan');
    }

    protected function _getPenerimaanRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Penerimaan');
    }

    protected function _getPetugasRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Petugas');
    }

    public function delete($idKategori){
        $kategori=$this->_getKategoriRepository()->find($idKategori);
        if(!empty($kategori) && !is_null($kategori)){

            $buku=$this->_getBukuRepository()->findBy(array("kategori"=>$idKategori));
            if(!empty($buku)){
                foreach ($buku as $bk) {
                    $this->entityManager->remove($bk);
                    $this->entityManager->flush();
                }
            }
            $this->entityManager->remove($kategori);
            $this->entityManager->flush();
        }
        return true;
    }

    public function getKategoriRepo(){

        return $this->_getKategoriRepository()->getQueryKategori();
    }

    public function getViewPenerimaan($id_penerimaan){
        return $this->_getDetailPenerimaanRepository()->findBy(array("penerimaan"=>$id_penerimaan));

    }
    public function savePenerimaan($data){
        $idBuku=$data["id_buku"];
        $total=$data["total"];
        $idPetugas=$data["id_petugas"];
        $tanggal=new \DateTime(date("Y-m-d"));


        $penerimaan=$this->_getPenerimaanRepository()->findOneBy(array("petugas"=>$idPetugas,
                                                                       "tanggal"=>$tanggal));


        if(!empty($penerimaan) && !is_null($penerimaan)){
            $total=$penerimaan->getTotal() + $total;
            $newPenerimaan=$penerimaan;
        }else{
            $newPenerimaan=new Penerimaan();            
        }


        $petugas=$this->_getPetugasRepository()->find($idPetugas);
        $buku=$this->_getBukuRepository()->find($idBuku);
        $totalBuku=$buku->getTotal() + $total;
        $totalTersedia=$buku->getTotalTersedia() + $total;
        //update jumlah buku
        $buku->setTotal($totalBuku);
        $buku->setTotalTersedia($totalTersedia);


        $newPenerimaan->setPetugas($petugas);
        $newPenerimaan->setTotal($total);
        $newPenerimaan->setTanggal();
        
        if(empty($penerimaan) || is_null($penerimaan)){
            $this->entityManager->persist($newPenerimaan);
        }

        $this->entityManager->flush();

        //save detail penerimaan
        $saveDetailPenerimaan=$this->saveDetailPenerimaan($newPenerimaan,$buku,$data["total"]);

        return array("level"=>"success");
    }

    public function saveDetailPenerimaan(Penerimaan $penerimaan,Buku $buku,$total){
        $newDetailPenerimaan=new DetailPenerimaan();
        $newDetailPenerimaan->setPenerimaan($penerimaan);
        $newDetailPenerimaan->setBuku($buku);
        $newDetailPenerimaan->setTotal($total);
        $newDetailPenerimaan->setTanggal();
        
        $this->entityManager->persist($newDetailPenerimaan);
        $this->entityManager->flush();
        return true;
    }

    //untuk perintah simpan atau edit 
    public function savePenerimaand($data){
        $id=$data["id"];
        $namaKategori=$data["namaKategori"];
        $noRak=$data["noRak"];
        $error="";

        /*
            jika id tidak kosong, berarti termasuk perintah edit
        */

        if(!empty($id) && !is_null($id)){
            //select ke entity kategori id yang dikirim ada atau tidak
            $kategori=$this->_getKategoriRepository()->find($id);

            //jika id kategori tidak ditemukan
            if(is_null($kategori) || empty($kategori)){
                $error="Kategori tidak ditemukan";
            }

        }else{
            //jika simpan data kategori
            $kategori=new Kategori();
        }

        $kategori->setNamaKategori($namaKategori);
        $kategori->setNoRak($noRak);

        //syarat save harus ada perintah persist. Adanya id berarti melakukan edit kalau tidak ada berarti save
        if(empty($id)){
            $this->entityManager->persist($kategori);
        }


        $this->entityManager->flush();

        //jika ada success
        if(empty($error)){
            $result=array("level"=>"success");
        }else{
            //jika error
            $result=array("level"=>"error","errMessage"=>$error);
        }

        return $result;
    }

    public function getPenerimaan(){

        $penerimaanEntity=$this->_getPenerimaanRepository()->findAll();
        return $penerimaanEntity;
    }


}
