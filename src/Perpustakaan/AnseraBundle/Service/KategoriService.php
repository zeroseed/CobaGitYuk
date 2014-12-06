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

/**
 * @DI\Service("perpus.service.kategori")
 * @DI\Tag("security.secure_service")
 */
class KategoriService
{

    protected $entityManager;

    protected $passwordEncoder;

    protected $session;
    protected $mailer;
	
    /**
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "passwordEncoder" = @DI\Inject("security.encoder.blowfish"),
   	 * 	   "session" = @DI\Inject("session"),
	 *     "mailer" = @DI\Inject("perpus.service.mailer")	 
	 *  
	 *  })
     */
    public function __construct(EntityManager $entityManager,BasePasswordEncoder $passwordEncoder,$session,MailerService $mailer) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session=$session;
   		$this->mailer = $mailer;
		

    }
    public function sendEmail(){
               
        $email=array("ansera.shah@gmail.com");
        
        $subject="Test email";
        $body ="<h1> Test Kirim Email </h1>";
        $body .= " Hi! This is email from localhost";
        
        $mail=$this->mailer->sendTemplatelessEmail($email,$subject,$body);
		print_r($email);
        return true;                
    }

    protected function _getKategoriRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Kategori');
    }

    protected function _getBukuRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Buku');
    }

    public function delete($idKategori){
        $kategori=$this->_getKategoriRepository()->find($idKategori);
        $error="";
		
        if(!empty($kategori) && !is_null($kategori)){
            $buku=$this->_getBukuRepository()->findBy(array("kategori"=>$idKategori));
            if(!empty($buku)){
            	$error="Maaf, kategori tidak bisa dihapus karena terdapat buku di dalamnya. Silahkan hapus buku terlebih dulu";
            }else{
	            $this->entityManager->remove($kategori);
	            $this->entityManager->flush();
			}
        }
		
		if(!empty($error)){
			$result=array("level"=>"error","errMessage"=>$error);
		}else{
			$result=array("level"=>"success");
		}
        return $result;
    }

public function getKategoriRepo(){

    return $this->_getKategoriRepository()->getQueryKategori();
}

    //untuk perintah simpan atau edit 
    public function saveOredit($data){
        $id=$data["id"];
        $namaKategori=$data["namaKategori"];
        $noRak=$data["noRak"];
        $error="";

        /*
            jika id tidak kosong, berarti termasuk perintah edit
        */
        
        $kategoriByNama=$this->_getKategoriRepository()->findOneBy(array("namaKategori"=>$namaKategori));
		if(!empty($kategoriByNama) && !is_null($kategoriByNama) && $kategoriByNama->getId() != $id ){
	     	$error="Nama Kategori sudah ada. Silahkan pilih nama kategori yang lain";
		}else{
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
		}
        //jika ada success
        if(empty($error)){
            $result=array("level"=>"success");
        }else{
            //jika error
            $result=array("level"=>"error","errMessage"=>$error);
        }

        return $result;
    }

    public function getCategories($idKategori=""){

        //jika ingin mengambil data dari salah satu idkategori
        if(!empty($idKategori)){
            $kategori=$this->_getKategoriRepository()->find($idKategori);
            $categories=array("id"=>$kategori->getId(),
                              "namaKategori"=>$kategori->getNamaKategori(),
                              "noRak"=>$kategori->getNoRak(),
                              );
        }else{
            //jika ingin mengambil semua data dari tabel kategori
      
            $listKategori=$this->_getKategoriRepository()->findBy(array(),array('id'=>'DESC'));
            $categories=array();
      
            if(!empty($listKategori) && !is_null($listKategori)){
                $i=1;
                foreach($listKategori as $kategori){

                    //kategori ditampung dalam sebuah array
                    $categories[]=array("id"=>$kategori->getId(),
                                      "namaKategori"=>$kategori->getNamaKategori(),
                                      "noRak"=>$kategori->getNoRak(),
                                      "no"=>$i
                                      );
                    $i++;
                }
            }
        }

        return $categories;
    }


}
