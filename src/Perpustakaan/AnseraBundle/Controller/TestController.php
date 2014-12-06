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

class TestController 
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
     * @Route("/test/delete/pengembalian", name="testdeletepengembalian")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Test:empty.html.twig")          
     */
    public function testDeletePengembalianAction(Request $request) {

            $detailPengembalian=$this->entityManager->getRepository('PerpustakaanAnseraBundle:DetailPengembalian')->findAll();
            if(!empty($detailPengembalian) && !is_null($detailPengembalian)){
                foreach($detailPengembalian as $pe){
                    $this->entityManager->remove($pe);
                    $this->entityManager->flush();
                }
            }
            
            $pengembalian=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Pengembalian')->findAll();
            if(!empty($pengembalian) && !is_null($pengembalian)){
                foreach($pengembalian as $pe){
                    $this->entityManager->remove($pe);
                    $this->entityManager->flush();
                }
            }

            echo " Data Sudah Dihapus";
            return true;
    }

    /**
     * @Route("/test/delete/peminjaman", name="testdeletepeminjaman")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Test:empty.html.twig")          
     */
    public function testDeletePeminjamanAction(Request $request) {

            $detailPengembalian=$this->entityManager->getRepository('PerpustakaanAnseraBundle:DetailPeminjaman')->findAll();
            if(!empty($detailPengembalian) && !is_null($detailPengembalian)){
                foreach($detailPengembalian as $pe){
                    $this->entityManager->remove($pe);
                    $this->entityManager->flush();
                }
            }
            
            $pengembalian=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Peminjaman')->findAll();
            if(!empty($pengembalian) && !is_null($pengembalian)){
                foreach($pengembalian as $pe){
                    $this->entityManager->remove($pe);
                    $this->entityManager->flush();
                }
            }

            echo " Data Sudah Dihapus";
            return true;
    }

    /**
     * @Route("/test/pengembalian", name="testpengembalian")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Test:empty.html.twig")          
     */
    public function testPengembalianAction(Request $request) {
        $idPeminjaman=$request->request->get("idPeminjaman");
        $idAnggota=$request->request->get("idAnggota");
        $idBuku=$request->request->get("idBuku");
        $idPetugas=$request->request->get("idPetugas");
        $status=$request->request->get("status");

        $data=array("id_peminjaman"=>$idPeminjaman,
                        "idAnggota"=>$idAnggota,
                        "idBuku"=>$idBuku,
                        "status"=>$status,
                        "idPetugas"=>$idPetugas
                    );

        $savePeminjaman=$this->pengembalianService->savePengembalian($data);
        return true;
    }

    /**
     * @Route("/test/mengembalikan", name="testmengembalikan")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Test:empty.html.twig")          
     */
    public function testMengembalianAction(Request $request) {
        $id=10;
        $data["id_peminjaman"]=10;
        $data["idBuku"]=10;
        $data["idPetugas"]=1;
        $data["idAnggota"]=8;
        $data["status"]="memperpanjang";

        $buku=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Buku')->find(10);
        //$update=$this->pengembalianService->updateDetailPeminjaman($data["id_peminjaman"],$data["idBuku"]);
        $savePengembalian=$this->pengembalianService->savePengembalian($data);

        echo " SELISIH : ";
        print_r($update);

        return true;
    }

    /**
     * @Route("/test", name="index")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Test:index.html.twig")          
     */
    public function indexAction(Request $request) {
    		
        $DETAILPEMINJAMAN=$this->entityManager->getRepository('PerpustakaanAnseraBundle:Peminjaman')->checkDetailPeminjaman(8,3);
    	$total=$this->bukuService->getTotalJenisBuku();
    	echo " line 183 : ";
    	print_r($DETAILPEMINJAMAN);
    	
        return true;
    }
}