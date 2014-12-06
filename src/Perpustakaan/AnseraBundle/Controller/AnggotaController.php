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
use Perpustakaan\AnseraBundle\Service\AnggotaService;


class AnggotaController 
{
    /**
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    public $entityManager;

    /**
     * @DI\Inject("knp_paginator")
     */
    public $paginator;

    /**
     * @DI\Inject("perpus.service.anggota")
     */
    public $anggotaService;

    /**
     * @DI\Inject("session")
     */
    public $session;

    /**
     * @DI\Inject("router")
     */
    public $router;
	
    /**
     * @Route("/anggota", name="anggota")
     * @Route("/anggota/edit/{id_anggota}", name="anggota_edit")
     * @Route("/anggota/delete/error", name="anggota_delete_error")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Anggota:anggota.html.twig")
     */
    public function anggotaAction(Request $request,$id_anggota="") {
		 $currentRoute = $request->attributes->get('_route');

        //1.untuk menampilkan data di tabel 
        $anggota=$this->anggotaService->getAnggota();
        //pada saat /anggota dipanggil, textbox noTelepon dan nama berisi kosong
        $error="";
        $noTelepon="";
        $alamat="";
        $nama="";
        //2. menentukan method post atau get
        $method=$request->getMethod();
        //3. jika ada id_anggota berarti dianggap edit
        if(!empty($id_anggota)){

            //untuk mengambil data sesuai id_anggota
            $anggotaById=$this->anggotaService->getAnggota($id_anggota);
            
            $noTelepon=$anggotaById["noTelepon"];
            $alamat=$anggotaById["alamat"];
            $nama=$anggotaById["nama"];
        }

        //4.jika pengguna melakukan klik tombol simpan
 
        if($method=="POST"){
            //cara symfony menghandle data dari POST Parameter
            /*
                nama dan noTelepon adalah nama textbox di twig
            */

            $nama=$request->request->get("nama");
            $noTelepon=$request->request->get("noTelepon");
            $alamat=$request->request->get("alamat");
            
            //validasi jika nama anggota kosong
            if(empty($nama)){
                
                $error="Nama anggota masih kosong";

            }elseif(strlen($nama) <=2) {
            	$error="Nama anggota minimal tiga karakter";
						
            }elseif(empty($noTelepon)){
                
                $error="No Telepon masih kosong";
            
            }elseif(strlen($noTelepon) <6){
                $error="No Telepon minimal tiga karakter";
            }elseif(empty($alamat)){
                $error="Alamat masih kosong";            	
            }elseif(strlen($alamat)<10){
                $error="Alamat minimal sepulu karakter";            
            }

            else{

                //data yang akan diubah atau disimpan
                $data=array("id"=>$id_anggota,
                            "nama"=>$nama,
                            "noTelepon"=>$noTelepon,"alamat"=>$alamat);
                
                // memanggil function untuk save atau edit data. Ada di file /Service/anggotaService.php                
                $saveanggota=$this->anggotaService->saveOrEdit($data);

                if($saveanggota["level"]=="error"){
                    $error=$saveanggota["errMessage"];
                }else{
                    //jika save atau edit berhasil kembali ke halaman anggota
                    return new RedirectResponse($this->router->generate('anggota'));                    
                }
            }
        }


        //untuk pangination menggunakan knpBundles
        $anggota=$this->paginator->paginate(
                        $anggota,$request->query->get('page',1),3
                    );
        
        //variable yang dikirim ke twig
        $result=array("success"=>true,
                      "anggota"=>$anggota,
                      "method"=>$method,
                      "pageName"=>"Anggota",
                      "nama"=>$nama,
                      "noTelepon"=>$noTelepon,
                      "alamat"=>$alamat
                    );

		$sessionDelete=$this->session->get("deleteAnggota");
				
        if($currentRoute=="anggota_delete_error" && !empty($sessionDelete) && $sessionDelete["level"]=="error"){
        	$error=$sessionDelete["errMessage"];
			
        }else{
        	$this->session->remove("deleteAnggota");
        }

        //jika ada error kasih response success=false, dan jenis errornya
        if(!empty($error)){
            $result["success"]=false;
            $result["error"]=$error;
        }

        return $result;
    }

    /**
     * @Route("/anggota/delete/{id_anggota}", name="anggota_delete")
     * @Method({"GET","POST"})
     */
    public function anggotaDeleteAction($id_anggota="") {
        $result=$this->anggotaService->delete($id_anggota);
		if($result["level"]=="success"){
	        return new RedirectResponse($this->router->generate('anggota'));                    
		}else{
			$this->session->set("deleteAnggota",$result);
	        return new RedirectResponse($this->router->generate('anggota_delete_error'));                    			
		}    
		
		
    }


}
