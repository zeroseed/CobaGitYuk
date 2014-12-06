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

class KategoriController 
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
     * @DI\Inject("perpus.service.kategori")
     */
    protected $kategoriService;

    /**
     * @DI\Inject("session")
     */
    protected $session;

    /**
     * @DI\Inject("router")
     */
    protected $router;

    /**
     * @Route("/kategori/latihanquery", name="kategori_latihanquery")
     * @Method({"GET","POST"})
     */
    public function kategoriLatihanQueryAction(Request $request,$id_kategori="") {
        $result=$this->kategoriService->getKategoriRepo();
        print_r($result);

        exit();    
    }


    /**
     * @Route("/kategori/latihan/dto", name="kategori_latihandto")
     * @Method({"GET","POST"})
     */
    public function kategoriLatihanDtoAction(){
        //menuliskan response, karena pada function ini tidak memanggil file twig apa pun
        $response=new Response();                                
        $response->headers->set('Content-Description', 'File Transfer');
        $response->headers->set('Content-Type', 'text/html');

        $kategori=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Kategori')->find(1);
        \Doctrine\Common\Util\Debug::dump($kategori);

        $kategoriDTO = DTO\KategoriDto::fromEntity($kategori);
        print_r($kategoriDTO);
        return $response;
    }

    /**
     * @Route("/kategori", name="kategori")
     * @Route("/kategori/edit/{id_kategori}", name="kategori_edit")
     * @Route("/kategori/delete/error", name="kategori_delete_error")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Kategori:kategori.html.twig")
     */
    public function kategoriAction(Request $request,$id_kategori="") {
    	//untuk mendapatkan name dari route , seperti : kategori, kategori_edit, dan kategori_delete_error
		 $currentRoute = $request->attributes->get('_route');

        //1.untuk menampilkan data di tabel 
        $kategori=$this->kategoriService->getCategories();

        //pada saat /kategori dipanggil, textbox noRak,search, dan namaKategori berisi kosong
        $error="";
        $noRak="";
        $search="";
        $namaKategori="";

        //2. menentukan method post atau get
        $method=$request->getMethod();

        //3. jika ada id_kategori berarti dianggap edit
        if(!empty($id_kategori)){

            //untuk mengambil data sesuai id_kategori
            $kategoriById=$this->kategoriService->getCategories($id_kategori);
            
            $noRak=$kategoriById["noRak"];
            
            $namaKategori=$kategoriById["namaKategori"];
        }

        //4.jika pengguna melakukan klik tombol simpan
 
        if($method=="POST"){
            //cara symfony menghandle data dari POST Parameter
            /*
                namaKategori dan noRak adalah nama textbox di twig
            */

            $namaKategori=$request->request->get("namaKategori");
            $noRak=$request->request->get("noRak");
            
            //validasi jika nama Kategori kosong
            if(empty($namaKategori)){
                
                $error="Nama Kategori masih kosong";

            }elseif(empty($noRak)){
                
                $error="No Rak masih kosong";
            
            }elseif(!is_numeric($noRak)){
            	$error="No Rak harus angka";	
            }else{

                //data yang akan diubah atau disimpan
                $data=array("id"=>$id_kategori,
                            "namaKategori"=>$namaKategori,
                            "noRak"=>$noRak);
                
                // memanggil function untuk save atau edit data. Ada di file /Service/kategoriService.php                
                $saveKategori=$this->kategoriService->saveOrEdit($data);

                if($saveKategori["level"]=="error"){
                    $error=$saveKategori["errMessage"];
                }else{
                    //jika save atau edit berhasil kembali ke halaman kategori
                    return new RedirectResponse($this->router->generate('kategori'));                    
                }
            }
        }

        //untuk pangination menggunakan knpBundles
        $categories=$this->paginator->paginate(
                        $kategori,$request->query->get('page',1),3
                    );
        
        //variable yang dikirim ke twig
        $result=array("success"=>true,
                      "categories"=>$categories,
                      "method"=>$method,
                      "namaKategori"=>$namaKategori,
                      "noRak"=>$noRak,
                      "pageName"=>"Kategori"
                    );
		$sessionDeleteKategori=$this->session->get("deleteKategori");			
		
		if($sessionDeleteKategori["level"]=="error" && $currentRoute=="kategori_delete_error"){
			$error=$sessionDeleteKategori["errMessage"];
		}else{
			$this->session->remove("deleteKategori");			
		}
		
        //jika ada error kasih response success=false, dan jenis errornya
        if(!empty($error)){
            $result["success"]=false;
            $result["error"]=$error;
        }

        return $result;
    }

    /**
     * @Route("/kategori/delete/{id_kategori}", name="kategori_delete")
     * @Method({"GET","POST"})
     */
    public function kategoriDeleteAction($id_kategori="") {
        $result=$this->kategoriService->delete($id_kategori);
		$this->session->set("deleteKategori",$result);
		if($result["level"]=="error"){
			$this->session->set("deleteKategori",$result);
	        return new RedirectResponse($this->router->generate('kategori_delete_error'));                    
		}else{
			$this->session->remove("deleteKategori");
			return new RedirectResponse($this->router->generate('kategori'));
		}    
    }


}