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
use Perpustakaan\AnseraBundle\Service\BukuService;
use Perpustakaan\AnseraBundle\Service\PeminjamanService;
use Perpustakaan\AnseraBundle\Service\PengembalianService;

use Perpustakaan\AnseraBundle\Dto as Dto;

class LaporanController 
{
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
     * @DI\Inject("perpus.service.pengembalian")
     */
    protected $pengembalianService;

    /**
     * @Route("/laporan", name="laporan")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Laporan:laporan.html.twig")
     */
    public function laporanAction(Request $request) {
    	$tglAwal=$request->request->get("tglAwal");
    	$tglAkhir=$request->request->get("tglAkhir");
    	$jenis=$request->request->get("jenis");
    	$method=$request->getMethod();
		if($method=="POST"){    	
			$parameters=array("tglAwal"=>$tglAwal,"tglAkhir"=>$tglAkhir,"jenis"=>$jenis);
	    	$this->session->set("laporan",$parameters);

	    	if($jenis==1){
	           return new RedirectResponse($this->router->generate('laporan_buku'));                        		
	    	}elseif($jenis==2){
	           return new RedirectResponse($this->router->generate('laporan_peminjaman'));                        		
	    	}elseif($jenis==3){
	           return new RedirectResponse($this->router->generate('laporan_pengembalian'));                        		
	    	}
	    }
		return array("success"=>true,"pageName"=>"Laporan");        
    }

    /**
     * @Route("/laporan/buku", name="laporan_buku")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Laporan:buku.html.twig")
     */
    public function cetakLaporanBukuAction(Request $request) {
    	$sessionLaporan=$this->session->get("laporan");
    	$tglAwal=$sessionLaporan["tglAwal"];
    	$tglAkhir=$sessionLaporan["tglAkhir"];
    	$jenis=$sessionLaporan["jenis"];

       	$laporan=$this->bukuService->getLaporan($sessionLaporan);
		return array("success"=>true,"pageName"=>"Laporan","laporan"=>$laporan,"jenis"=>$jenis);        
    }


    /**
     * @Route("/laporan/peminjaman", name="laporan_peminjaman")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Laporan:peminjaman.html.twig")
     */
    public function cetakLaporanPeminjamanAction(Request $request) {
        $sessionLaporan=$this->session->get("laporan");
        $tglAwal=$sessionLaporan["tglAwal"];
        $tglAkhir=$sessionLaporan["tglAkhir"];
        $jenis=$sessionLaporan["jenis"];

        $laporan=$this->peminjamanService->getLaporan($sessionLaporan);
        return array("success"=>true,"pageName"=>"Laporan","laporan"=>$laporan,"jenis"=>$jenis);        
    }


    /**
     * @Route("/laporan/pengembalian", name="laporan_pengembalian")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Laporan:pengembalian.html.twig")
     */
    public function cetakLaporanPengembalianAction(Request $request) {
        $sessionLaporan=$this->session->get("laporan");
        $tglAwal=$sessionLaporan["tglAwal"];
        $tglAkhir=$sessionLaporan["tglAkhir"];
        $jenis=$sessionLaporan["jenis"];

        $laporan=$this->pengembalianService->getLaporan($sessionLaporan);
        return array("success"=>true,"pageName"=>"Laporan","laporan"=>$laporan,"jenis"=>$jenis);        
    }


}