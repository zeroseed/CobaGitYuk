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
use Perpustakaan\AnseraBundle\Service\PeminjamanService;
use Perpustakaan\AnseraBundle\Service\BukuService;
use Perpustakaan\AnseraBundle\Service\AnggotaService;
use Perpustakaan\AnseraBundle\Service\PengembalianService;

use Perpustakaan\AnseraBundle\Dto as Dto;

class PengembalianController 
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
     * @DI\Inject("perpus.service.anggota")
     */
    protected $anggotaService;

    /**
     * @DI\Inject("perpus.service.pengembalian")
     */
    protected $pengembalianService;

    /**
     * @DI\Inject("perpus.service.buku")
     */
    protected $bukuService;

    /**
     * @DI\Inject("perpus.service.peminjaman")
     */
    protected $peminjamanService;

    /**
     * @DI\Inject("session")
     */
    protected $session;

    /**
     * @DI\Inject("router")
     */
    protected $router;


    /**
     * @Route("/pengembalian", name="pengembalian")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Pengembalian:pengembalian.html.twig")          
     */
    public function pengembalianAction(Request $request) {
        $session=$this->session->get("petugas");
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }

        //1.untuk menampilkan data di tabel 
        $anggota=$this->anggotaService->getAnggota();
        $peminjaman=$this->peminjamanService->getPeminjaman();
        
        $error="";
        $idAnggota="";

        $method=$request->getMethod();

        if($method=="POST"){

            $idAnggota=$request->request->get("idAnggota");
            
            if(empty($idAnggota)){
                $error="Silahkan pilih ID Anggota masih kosong";
            }else{
                $this->session->set("id_anggota",$idAnggota);
                $this->session->set("id_peminjaman",$id_peminjaman);

                return new RedirectResponse($this->router->generate('detail_peminjaman'));                    
            }
        }

        $peminjaman=$this->paginator->paginate(
                        $peminjaman,$request->query->get('page',1),3
                    );
        

        $result=array("pageName"=>"Pengembalian",
                        "error"=>$error,
                        "anggota"=>$anggota,"idAnggota"=>$idAnggota,"peminjaman"=>$peminjaman);
        return $result;

    }

    /**
     * @Route("/pengembalian/listpeminjaman/{id_peminjaman}", name="pengembalian_view")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Pengembalian:view.html.twig")          
     */
    public function pengembalianListPeminjamanAction(Request $request,$id_peminjaman) {
        $session=$this->session->get("petugas");
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }

            $peminjaman=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Peminjaman')->find($id_peminjaman);                
            if(!empty($peminjaman) && !is_null($peminjaman)){
                $this->session->set("id_anggota",$peminjaman->getAnggota()->getId());
                $idAnggota=$peminjaman->getAnggota()->getId();
            }else{
                return new RedirectResponse($this->router->generate('pengembalian'));                                    
            }
            
            $this->session->set("id_peminjaman",$id_peminjaman);

            $idBuku=$request->request->get("idBuku");
            $status=$request->request->get("status");

            $method=$request->getMethod();
            $error="";

            $anggota=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Anggota')->find($idAnggota);
            $namaAnggota=$anggota->getNama();

            if($method=="POST"){
                $sessionPetugas=$this->session->get("petugas");
                $idPetugas=$sessionPetugas["id"];

                $data=array("id_peminjaman"=>$id_peminjaman,
                            "idAnggota"=>$idAnggota,
                            "idBuku"=>$idBuku,
                            "status"=>$status,
                            "idPetugas"=>$idPetugas);
                $savePeminjaman=$this->pengembalianService->savePengembalian($data);
                
                if($savePeminjaman["level"]=="error"){
                    $error=$savePeminjaman["errMessage"];
                }else{
                    return new RedirectResponse($this->router->generate('pengembalian_tanya'));                    
                }

            }

            $detailPeminjaman=$this->peminjamanService->getDetailPeminjaman($idAnggota);
            $buku=$this->peminjamanService->getBukuPinjaman($id_peminjaman);
            if(empty($buku)){
                $ket=true;
            }else{
                $ket=false;
            }
            
            //untuk pangination menggunakan knpBundles
            $detailPeminjaman=$this->paginator->paginate(
                            $detailPeminjaman,$request->query->get('page',1),3
                        );
            

            $result=array("pageName"=>"Pengembalian",
                         "buku"=>$buku,
                         "namaAnggota"=>$namaAnggota,
                         "idBuku"=>$idBuku,
                         "detailPeminjaman"=>$detailPeminjaman,
                         "ket"=>$ket
                         );
            if(!empty($error)){
                $result["error"]=$error;
                $result["success"]=false;
            }else{
                $result["success"]=true;
            }

            return $result;

    }

    /**
     * @Route("/pengembalian/tanya", name="pengembalian_tanya")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Pengembalian:tanya.html.twig")     
     */
    public function pengembalianTanyaAction(Request $request) {
        $session=$this->session->get("petugas");
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }

            $idAnggota=$this->session->get("id_anggota");
            $result=array("pageName"=>"Pengembalian",
                         "idAnggota"=>$idAnggota);
            return $result;
    }

}