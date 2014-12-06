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

use Perpustakaan\AnseraBundle\Entity\Buku;
use Perpustakaan\AnseraBundle\Entity\Kategori;

/**
 * @DI\Service("perpus.service.buku")
 * @DI\Tag("security.secure_service")
 */
class BukuService
{

    protected $entityManager;

    protected $passwordEncoder;

    protected $session;

    protected $kategoriService;


    /**
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "passwordEncoder" = @DI\Inject("security.encoder.blowfish"),
   	 * 	   "session" = @DI\Inject("session"),
     *     "kategoriService"=@DI\Inject("perpus.service.kategori")
	 *  })
     */
    public function __construct(EntityManager $entityManager,BasePasswordEncoder $passwordEncoder,
                                $session,KategoriService $kategoriService) {

        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session=$session;
        $this->kategoriService=$kategoriService;

    }
    public function getLaporan(){
      $laporan=$this->_getBukuRepository()->findAll();
      return $laporan;
    }
    public function parseCSV($fileHandle){
        $row=0;
        while (($data = fgetcsv($fileHandle, 1000, ",")) !== FALSE) {
                        
            //First line is for heading, skip it
            $row++;
            if($row==1) {
                continue;
            }

            if(!empty($data[0])){
                $newBuku=new Buku();
                $kategori=$this->_getKategoriRepository()->find($data[0]);
                
                $judul=$data[1];
                $penulis=$data[2];
                $penerbit=$data[3];
                $thnTerbit=$data[4];
                $isbn=$data[5];
                $total=$data[6];

                $newBuku->setKategori($kategori);
                $newBuku->setJudul($judul);
                $newBuku->setPenulis($penulis);
                $newBuku->setPenerbit($penerbit);
                $newBuku->setTahunTerbit($thnTerbit);
                $newBuku->setIsbn($isbn);
                $newBuku->setTotal($total);
                $this->entityManager->persist($newBuku);
                $this->entityManager->flush();
            }
        }

        return array("level"=>"success");
    }

    
    public function getBooks($idBuku="",$type="all"){
        if(!empty($idBuku)){
            $buku=$this->_getBukuRepository()->find($idBuku);
            $books=array("id"=>$buku->getId(),
                              "kategori"=>array("id"=>$buku->getKategori()->getId(),
                                                "namaKategori"=>$buku->getKategori()->getNamaKategori(),
                                                "noRak"=>$buku->getKategori()->getNoRak()
                                                ),
                              "judul"=>$buku->getJudul(),
                              "penulis"=>$buku->getPenulis(),
                              "tahun_terbit"=>$buku->getTahunTerbit(),
                              "penerbit"=>$buku->getPenerbit(),
                              "isbn"=>$buku->getIsbn(),
                              "total"=>$buku->getTotal()
                              );
        }else{
            if($type=="dropDown"){
              $books=$this->_getBukuRepository()->getTersedia();             
            }else{
              $listBuku=$this->_getBukuRepository()->findBy(array(),array('id'=>'DESC'));
              $books=array();
              if(!empty($listBuku) && !is_null($listBuku)){
                  $i=1;
                  foreach($listBuku as $buku){

                      $books[]=array("id"=>$buku->getId(),
                                 "no"=>$i,
                                "kategori"=>array("id"=>$buku->getKategori()->getId(),
                                                  "namaKategori"=>$buku->getKategori()->getNamaKategori(),
                                                  "noRak"=>$buku->getKategori()->getNoRak()
                                                  ),
                                "judul"=>$buku->getJudul(),
                                "penulis"=>$buku->getPenulis(),
                                "tahun_terbit"=>$buku->getTahunTerbit(),
                                "penerbit"=>$buku->getPenerbit(),
                                "isbn"=>$buku->getIsbn(),
                                "total"=>$buku->getTotal(),
                                "totalKeluar"=>$buku->getTotalKeluar(),
                                "totalHilangRusak"=>$buku->getRusakHilang()
                                );

                      $i++;
                  }
              }
            }
        }

        return $books;
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

    protected function _getDetailPeminjamanRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:DetailPeminjaman');
    }
    
    protected function _getDetailPengembalianRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:DetailPengembalian');
    }
	
    public function delete($idBuku){
        $buku=$this->_getBukuRepository()->find($idBuku);
        $error="";
		$isError=false;
        if(!empty($buku) && !is_null($buku)){
        	$penerimaan=$this->_getDetailPenerimaanRepository()->findOneBy(array("buku"=>$buku->getId()));
			$peminjaman=$this->_getDetailPeminjamanRepository()->findOneBy(array("buku"=>$buku->getId()));
			$pengembalian=$this->_getDetailPengembalianRepository()->findOneBy(array("buku"=>$buku->getId()));
			
	
			if (!empty($penerimaan) && !is_null($penerimaan)){
				$isError=true;
			}
			
			if(!empty($peminjaman) && !is_null($peminjaman) && count($peminjaman) == 0){
				$isError=true;
			} 
			
			if(!empty($pengembalian) && !is_null($pengembalian) && count($pengembalian) == 0){
				$isError=true;
			}
		
			if($isError==false){	
	            $this->entityManager->remove($buku);
    	        $this->entityManager->flush();
			}
		}
     
		if($isError==true){
			$result=array("level"=>"error","errMessage"=>"Maaf, buku tersebut tidak bisa dihapus karena ada beberapa transaksi");
		}else{
			$result=array("level"=>"success");
		}
		
        return $result;
    }

    //untuk perintah simpan atau edit 
    public function saveOredit($data){
        $id=$data["id"];
        $judul=$data["judul"];
        $kategori_id=$data["kategori"];
        $penerbit=$data["penerbit"];
        $penulis=$data["penulis"];
        $isbn=$data["isbn"];
        $tahunTerbit=$data["tahunTerbit"];


        $error="";
        
        $kategori=$this->_getKategoriRepository()->find($kategori_id);
        if(!empty($kategori) && !is_null($kategori)){
        

          /*
              jika id tidak kosong, berarti termasuk perintah edit
          */

          if(!empty($id) && !is_null($id)){
              //select ke entity buku id yang dikirim ada atau tidak
              $buku=$this->_getBukuRepository()->find($id);

              //jika id kategori tidak ditemukan
              if(is_null($buku) || empty($buku)){
                  $error="Buku tidak ditemukan";
              }

          }else{
              //jika simpan data buku
              $buku=new Buku();
          }



          $buku->setJudul($judul);
          $buku->setPenerbit($penerbit);
          $buku->setPenulis($penulis);
          $buku->setTahunTerbit($tahunTerbit);
          $buku->setISBN($isbn);
          $buku->setKategori($kategori);

          //syarat save harus ada perintah persist. Adanya id berarti melakukan edit kalau tidak ada berarti save
          if(empty($id)){
              $this->entityManager->persist($buku);
          }


          $this->entityManager->flush();
      }else{
        $error="Kategori tidak ditemukan";
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

	public function getTotalJenisBuku(){
		$total=$this->_getBukuRepository()->getTotalJenisBuku();
		return $total;
	}
}
