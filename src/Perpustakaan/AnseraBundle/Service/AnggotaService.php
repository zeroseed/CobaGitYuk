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

use Perpustakaan\AnseraBundle\Entity\Anggota;
use Perpustakaan\AnseraBundle\Entity\Pengembalian;
use Perpustakaan\AnseraBundle\Entity\Peminjaman;

/**
 * @DI\Service("perpus.service.Anggota")
 * @DI\Tag("security.secure_service")
 */
class AnggotaService
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

    protected function _getAnggotaRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Anggota');
    }

    protected function _getPeminjamanRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Peminjaman');
    }

    protected function _getPengembalianRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Pengembalian');
    }

    public function delete($idAnggota){
        $anggota=$this->_getAnggotaRepository()->find($idAnggota);
        $isDelete=true;
		
        if(!empty($anggota) && !is_null($anggota)){
        	$peminjaman=$this->_getPeminjamanRepository()->findOneBy(array("anggota"=>$anggota->getId()));
			$pengembalian=$this->_getPengembalianRepository()->findOneBy(array("anggota"=>$anggota->getId()));
			if(!empty($peminjaman) && !is_null($peminjaman)){
				$isDelete=false;
			}
			
			if(!empty($pengembalian) && !is_null($pengembalian)){
				$isDelete=false;
			}
			
			if($isDelete==true){	
	            $this->entityManager->remove($anggota);
    	        $this->entityManager->flush();
				$result=array("level"=>"success");
			}else{
				$result=array("level"=>"error","errMessage"=>"Maaf, Anggota tersebut tidak dapat dihapus karena ada beberapa transaksi");
			}
        }else{
       		$result=array("level"=>"error","errMessage"=>"Anggota tersebut tidak ditemukan");
        }
		
        return $result;
    }

    //untuk perintah simpan atau edit 
    public function saveOredit($data){
        $id=$data["id"];
        $nama=$data["nama"];
        $noTelepon=$data["noTelepon"];
        $alamat=$data["alamat"];

        $error="";

        /*
            jika id tidak kosong, berarti termasuk perintah edit
        */

        if(!empty($id) && !is_null($id)){
            //select ke entity anggota id yang dikirim ada atau tidak
            $anggota=$this->_getAnggotaRepository()->find($id);

            //jika id anggota tidak ditemukan
            if(is_null($anggota) || empty($anggota)){
                $error="anggota tidak ditemukan";
            }

        }else{
            //jika simpan data anggota
            $anggota=new Anggota();
        }

        $anggota->setAlamat($alamat);
        $anggota->setNoTelepon($noTelepon);
        $anggota->setNama($nama);
        $anggota->setTanggalBergabung();

        //syarat save harus ada perintah persist. Adanya id berarti melakukan edit kalau tidak ada berarti save
        if(empty($id)){
            $this->entityManager->persist($anggota);
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

    public function getAnggota($idAnggota=""){

        //jika ingin mengambil data dari salah satu idanggota
        if(!empty($idAnggota)){
            $anggotaEntity=$this->_getAnggotaRepository()->find($idAnggota);
            $anggota=array("id"=>$anggotaEntity->getId(),
                              "nama"=>$anggotaEntity->getNama(),
                              "noTelepon"=>$anggotaEntity->getNoTelepon(),
                              "alamat"=>$anggotaEntity->getAlamat(),
                              "tglBergabung"=>$anggotaEntity->getTanggalBergabung()
                              );
        }else{
            //jika ingin mengambil semua data dari tabel anggota
      
            $listAnggota=$this->_getAnggotaRepository()->findBy(array(),array('id'=>'DESC'));
            $anggota=array();
      
            if(!empty($listAnggota) && !is_null($listAnggota)){
                $i=1;
                foreach($listAnggota as $a){

                    //anggota ditampung dalam sebuah array
                    $anggota[]=array("id"=>$a->getId(),
                                      "nama"=>$a->getNama(),
                                      "noTelepon"=>$a->getNoTelepon(),
                                      "no"=>$i,
                                      "tanggalBergabung"=>$a->getTanggalBergabung(),
                                      "alamat"=>$a->getAlamat()

                                      );
                    $i++;
                }
            }
        }

        return $anggota;
    }


}
