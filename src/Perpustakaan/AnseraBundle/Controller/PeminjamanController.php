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

use Perpustakaan\AnseraBundle\Dto as Dto;

class PeminjamanController 
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
     * @Route("/peminjaman", name="peminjaman")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Peminjaman:peminjaman.html.twig")     
     */
    public function peminjamanAction(Request $request) {
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
                $session=$this->session->set("id_anggota",$idAnggota);
                return new RedirectResponse($this->router->generate('detail_peminjaman'));                    
            }
        }

        $peminjaman=$this->paginator->paginate(
                        $peminjaman,$request->query->get('page',1),3
                    );
        

        $result=array("pageName"=>"Peminjaman","error"=>$error,"anggota"=>$anggota,"idAnggota"=>$idAnggota,"peminjaman"=>$peminjaman);
        return $result;

    }

    /**
     * @Route("/detailpeminjaman", name="detail_peminjaman")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Peminjaman:detail.html.twig")     
     */
    public function detailPeminjamanAction(Request $request) {
        $session=$this->session->get("petugas");
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }

            $idAnggota=$this->session->get("id_anggota");
            $idBuku=$request->request->get("idBuku");
            $buku=$this->bukuService->getBooks("","dropDown");
            $method=$request->getMethod();
            $getTotalPinjamBuku=$this->peminjamanService->getTotalPinjamBuku($idAnggota);
            $jumlahBuku=$getTotalPinjamBuku[0]["total"];
            $error="";
            
			//mengambil data total jenis buku yang tersedia
			$totalJenisBuku=$this->bukuService->getTotalJenisBuku();
			
            $anggota=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Anggota')->find($idAnggota);
            $namaAnggota=$anggota->getNama();

            if($method=="POST"){
                if(empty($idBuku)){
                    $error="Silahkan pilih buku yang dipinjam";
                }else{
                    $sessionPetugas=$this->session->get("petugas");
                    $idPetugas=$sessionPetugas["id"];

                    $data=array("idAnggota"=>$idAnggota,"idBuku"=>$idBuku,"idPetugas"=>$idPetugas);
                    $savePeminjaman=$this->peminjamanService->savePeminjaman($data);
                    
                    if($savePeminjaman["level"]=="error"){
                        $error=$savePeminjaman["errMessage"];
                    }else{
                        $getTotalPinjamBuku=$this->peminjamanService->getTotalPinjamBuku($idAnggota);
                        $jumlahBuku=$getTotalPinjamBuku[0]["total"];
                        if($jumlahBuku < 3 && $totalJenisBuku > 1){
                            return new RedirectResponse($this->router->generate('peminjaman_tanya'));                    
                        }else{
                            return new RedirectResponse($this->router->generate('peminjaman'));                                             
                        }
                    }

                }
            }

            $detailPeminjaman=$this->peminjamanService->getDetailPeminjaman($idAnggota);

            //untuk pangination menggunakan knpBundles
            $detailPeminjaman=$this->paginator->paginate(
                            $detailPeminjaman,$request->query->get('page',1),3
                        );
            

            $result=array("pageName"=>"Peminjaman",
                         "buku"=>$buku,
                         "namaAnggota"=>$namaAnggota,
                         "idBuku"=>$idBuku,
                         "jumlahBuku"=>$jumlahBuku,
                         "detailPeminjaman"=>$detailPeminjaman);
            if(!empty($error)){
                $result["error"]=$error;
                $result["success"]=false;
            }else{
                $result["success"]=true;
            }

            return $result;

    }

    /**
     * @Route("/detailpeminjaman/tanya", name="peminjaman_tanya")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Peminjaman:tanya.html.twig")     
     */
    public function peminjamanTanyaAction(Request $request) {
        $session=$this->session->get("petugas");
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }

            $idAnggota=$this->session->get("id_anggota");
            $result=array("pageName"=>"Peminjaman",
                         "idAnggota"=>$idAnggota);
            return $result;
    }
    /**
     * @Route("/peminjaman/delete/{id_detail_pinjam}", name="detailpinjam_delete")
     * @Method({"GET","POST"})
     */
    public function deleteDetailPeminjamanAction(Request $request,$id_detail_pinjam="") {
        if(!empty($id_detail_pinjam) && !is_null($id_detail_pinjam)){
            $delete=$this->peminjamanService->deleteDetailPeminjaman($id_detail_pinjam);
        }

       return new RedirectResponse($this->router->generate('detail_peminjaman'));                    
        
    }

    /**
     * @Route("/peminjaman/view/{id_peminjaman}", name="peminjaman_view")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Peminjaman:view.html.twig")     
     */
    public function peminjamanViewsAction(Request $request,$id_peminjaman="") {
        $session=$this->session->get("petugas");
        if(empty($session) || is_null($session)){
            return new RedirectResponse($this->router->generate('login'));                    
        }
        
            $method=$request->getMethod();
            $peminjaman=$this->peminjamanService->getPeminjaman($id_peminjaman);

            if(!empty($peminjaman) && !is_null($peminjaman)){
                $namaAnggota=$peminjaman->getAnggota()->getNama();
                $namaPetugas=$peminjaman->getPetugas()->getNama();
                $tanggalPinjam=$peminjaman->getTanggalPinjam();
            }else{
                return new RedirectResponse($this->router->generate('peminjaman'));                    
            }

            $detailPeminjaman=$this->peminjamanService->getDetailPeminjaman("",$id_peminjaman);
           
            //untuk pangination menggunakan knpBundles
            $detailPeminjaman=$this->paginator->paginate(
                            $detailPeminjaman,$request->query->get('page',1),3
                        );
            

            $result=array("pageName"=>"Peminjaman",
                          "namaAnggota"=>$namaAnggota,
                          "tanggalPinjam"=>$tanggalPinjam,
                          "namaPetugas"=>$namaPetugas,
                          "detailPeminjaman"=>$detailPeminjaman);
            return $result;

    }    
}