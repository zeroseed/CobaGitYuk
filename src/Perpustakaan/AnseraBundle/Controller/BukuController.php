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
use Perpustakaan\AnseraBundle\Entity\Buku;

//untuk memudahkan mengarahkan template yang dipakai
use FOS\RestBundle\Controller\Annotations as FOS;

//include service yg yang digunakan
use Perpustakaan\AnseraBundle\Service\BukuService;
use Perpustakaan\AnseraBundle\Service\KategoriService;


class BukuController 
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
     * @DI\Inject("perpus.service.buku")
     */
    protected $bukuService;

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
     * @Route("/buku", name="buku")
     * @Route("/buku/edit/{id_Buku}", name="buku_edit")
     * @Route("/buku/delete/error", name="buku_delete_error")
	 * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Buku:buku.html.twig")
     */
    public function BukuAction(Request $request,$id_Buku="") {

		 $currentRoute = $request->attributes->get('_route');
    	
        //list buku yang ditampilkan pada tabel
        $buku=$this->bukuService->getBooks();

        $method=$request->getMethod();
        
        //deklarasi variable sebagai value di textbox/dropdown
        $error="";
        $noRak="";
        $search="";
        $namaBuku="";
        $kategori="";
        $penulis="";
        $penerbit="";
        $tahunTerbit="";
        $isbn="";
        $judul="";

        if(!empty($id_Buku)){
            $BukuById=$this->bukuService->getBooks($id_Buku);

            $judul=$BukuById["judul"];
            $isbn=$BukuById["isbn"];
            $penerbit=$BukuById["penerbit"];
            $penulis=$BukuById["penulis"];
            $kategori=$BukuById["kategori"]["id"];
            $tahunTerbit=$BukuById["tahun_terbit"];

        }


        if($method=="POST"){
            $judul=$request->request->get("judul");
            $isbn=$request->request->get("isbn");
            $penulis=$request->request->get("penulis");
            $kategori=$request->request->get("kategori");
            $tahunTerbit=$request->request->get("tahunTerbit");
            $penerbit=$request->request->get("penerbit");
			$thnSekarang=date("Y");
		
            if(empty($kategori)){
                $error="Silahkan pilih kategori";
            }elseif(empty($judul)){
                $error="Judul masih kosong";
            }elseif(empty($penulis)){
                $error="Penulis masih kosong";
            }elseif(empty($tahunTerbit)){
                $error="Tahun Terbit masih kosong";                
            }elseif(!is_numeric($tahunTerbit)){
            	$error="Tahun terbit harus angka";
            }elseif($tahunTerbit > $thnSekarang){
            	$error="Tahun terbit maksimal tahun ".$thnSekarang;
            }
            elseif(empty($penerbit)){
                $error="Penerbit masih kosong";                                
            }elseif(empty($isbn)){
                $error="ISBN masih kosong";                                
            }else{
                $data=array("id"=>$id_Buku,
                            "judul"=>$judul,
                            "kategori"=>$kategori,
                            "penulis"=>$penulis,
                            "penerbit"=>$penerbit,
                            "tahunTerbit"=>$tahunTerbit,
                            "isbn"=>$isbn
                            );

                $saveBuku=$this->bukuService->saveOrEdit($data);

                if($saveBuku["level"]=="error"){
                    $error=$saveBuku["errMessage"];
                }else{
                    return new RedirectResponse($this->router->generate('buku'));                    
                }
            }
        }

        $books=$this->paginator->paginate(
                        $buku,$request->query->get('page',1),3
                    );
        $categories=$this->kategoriService->getCategories();

        $result=array("success"=>true,
                      "books"=>$books,
                      "method"=>$method,
                      "judul"=>$judul,
                      "penulis"=>$penulis,
                      "penerbit"=>$penerbit,
                      "isbn"=>$isbn,
                      "tahunTerbit"=>$tahunTerbit,
                      "id_kategori"=>$kategori,
                      "pageName"=>"Buku",
                      "categories"=>$categories
                    );
		$sessionDelete=$this->session->get("deleteBuku");
				
        if($currentRoute=="buku_delete_error" && !empty($sessionDelete) && $sessionDelete["level"]=="error"){
        	$error=$sessionDelete["errMessage"];
        }else{
        	$this->session->remove("deleteBuku");
        }
        if(!empty($error)){
            $result["success"]=false;
            $result["error"]=$error;
        }

        return $result;
    }

    /**
     * @Route("/buku/delete/{id_Buku}", name="buku_delete")
     * @Method({"GET","POST"})
     */
    public function BukuDeleteAction($id_Buku="") {
        $result=$this->bukuService->delete($id_Buku);
		if($result["level"]=="error"){
			$this->session->set("deleteBuku",$result);
		
	       return new RedirectResponse($this->router->generate('buku_delete_error'));                    			
		}else{
	        return new RedirectResponse($this->router->generate('buku'));                    
		}    
    }


}