<?php

//nama bundle yang dipakai
namespace Perpustakaan\AnseraBundle\Controller;

//component symfony 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

//digunakan untuk annotation seperti contoh dibawah yaitu entityManager
use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;

//entity yang digunakan
use Perpustakaan\AnseraBundle\Entity\Petugas;

//untuk memudahkan mengarahkan template yang dipakai
use FOS\RestBundle\Controller\Annotations as FOS;

//include service yg yang digunakan
use Perpustakaan\AnseraBundle\Service\KategoriService;

use Perpustakaan\AnseraBundle\Dto as Dto;

class PenerimaanController 
{

    /**
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    protected $entityManager;

    /**
     * @DI\Inject("knp_paginator")
     */
    protected $paginator;

    /**
     * @DI\Inject("perpus.service.penerimaan")
     */
    protected $penerimaanService;

    /**
     * @DI\Inject("perpus.service.buku")
     */
    protected $bukuService;

    /**
     * @DI\Inject("session")
     */
    protected $session;

    /**
     * @DI\Inject("router")
     */
    protected $router;

    /**
     * @Route("/penerimaan/view/{id_penerimaan}", name="penerimaan_view")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Penerimaan:view.html.twig")
     */
    public function penerimaanViewAction(Request $request,$id_penerimaan="") {
    	$session=$this->session->get("petugas");
		
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }

    //1.untuk menampilkan data di tabel 
        $penerimaan=$this->penerimaanService->getViewPenerimaan($id_penerimaan);
        //untuk pangination menggunakan knpBundles
        $penerimaan=$this->paginator->paginate(
                        $penerimaan,$request->query->get('page',1),3
                    );
        
        //variable yang dikirim ke twig
        $result=array("success"=>true,
                      "detailPenerimaan"=>$penerimaan,
                      "pageName"=>"Penerimaan"
                    );
        return $result;
    }

    /**
     * @Route("/penerimaan", name="penerimaan")
     * @Route("/penerimaan/edit/{id_penerimaan}", name="penerimaan_edit")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Penerimaan:penerimaan.html.twig")
     */
    public function penerimaanAction(Request $request,$id_penerimaan="") {
    	$session=$this->session->get("petugas");
		
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }

        //1.untuk menampilkan data di tabel 
        $penerimaan=$this->penerimaanService->getPenerimaan();

        $error="";
        $id_buku="";
        $total="";
       
        $buku=$this->bukuService->getBooks();

        //2. menentukan method post atau get
        $method=$request->getMethod();

        //3. jika ada id_penerimaan berarti dianggap edit
        if(!empty($id_penerimaan)){

            //untuk mengambil data sesuai id_kategori
            $penerimaanById=$this->penerimaanService->getPenerimaan($id_penerimaan);
            
            $id_buku=$penerimaanById["buku"]["id"];
            $total=$penerimaanById["total"];
     
        }

        //4.jika pengguna melakukan klik tombol simpan
 
        if($method=="POST"){
            //cara symfony menghandle data dari POST Parameter
            /*
                namaKategori dan noRak adalah nama textbox di twig
            */

            $id_buku=$request->request->get("id_buku");
            $total=$request->request->get("total");
            
            //validasi jika nama buku kosong
            if(empty($id_buku)){
                
                $error="Buku masih kosong";

            }elseif(empty($total) && $total<=0){
                
                $error="Silahkan masukan jumlah total penerimaan";
            
            }else{
            	$session=$this->session->get("petugas");
            	$id_petugas=$session["id"];
                //data yang akan diubah atau disimpan
                $data=array("id"=>$id_penerimaan,
                            "id_buku"=>$id_buku,
                            "id_petugas"=>$id_petugas,
                            "total"=>$total);
                
                // memanggil function untuk save atau edit data. Ada di file /Service/kategoriService.php                
                $savePenerimaan=$this->penerimaanService->savePenerimaan($data);

                if($savePenerimaan["level"]=="error"){
                    $error=$savePenerimaan["errMessage"];
                }else{
                    //jika save atau edit berhasil kembali ke halaman kategori
                    return new RedirectResponse($this->router->generate('penerimaan'));                    
                }
            }
        }

        //untuk pangination menggunakan knpBundles
        $penerimaan=$this->paginator->paginate(
                        $penerimaan,$request->query->get('page',1),3
                    );
        
        //variable yang dikirim ke twig
        $result=array("success"=>true,
                      "penerimaan"=>$penerimaan,
                      "method"=>$method,
                      "total"=>$total,
                      "id_buku"=>$id_buku,
                      "pageName"=>"Penerimaan",
                      "buku"=>$buku
                    );

        //jika ada error kasih response success=false, dan jenis errornya
        if(!empty($error)){
            $result["success"]=false;
            $result["error"]=$error;
        }

        return $result;
    }

    /**
     * @Route("/penerimaan/delete/{id_penerimaan}", name="penerimaan_delete")
     * @Method({"GET","POST"})
     */
    public function penerimaanDeleteAction($id_penerimaan="") {
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }
      
        $result=$this->penerimaanService->delete($id_penerimaan);
        return new RedirectResponse($this->router->generate('penerimaan'));                    
        
    }

}