<?php

namespace Perpustakaan\AnseraBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Perpustakaan\AnseraBundle\Form\FormKategori;
use Perpustakaan\AnseraBundle\Form\FormElement;

use Perpustakaan\AnseraBundle\Entity\Kategori;
use FOS\RestBundle\Controller\Annotations as FOS;
use Perpustakaan\AnseraBundle\Service\BukuService;
use Perpustakaan\AnseraBundle\Service\PetugasService;

use Latihan\AnseraBundle\Service\BuahService;
use Latihan\AnseraBundle\Service\SayuranService;

class LatihanController 
{
    /**
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    protected $entityManager;

    /**
     * @DI\Inject("form.factory")
     */
    protected $formFactory;

    /**
     * @DI\Inject("perpus.service.buku")
     */
    protected $bukuService;

    /**
     * @DI\Inject("perpus.service.kategori")
     */
    protected $kategoriService;

    /**
     * @DI\Inject("perpus.service.petugas")
     */
    protected $petugasService;


    /**
     * @Route("/latihan/simpan-kategori", name="latihansimpankategori")
	 * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:latihan.html.twig")
     */
    public function latihanAction() {
    	$entityManager=$this->entityManager;

    	$newKategori=new Kategori ();
		$newKategori->setNamaKategori('ilmu komputer');
		$newKategori->setNoRak(1);
		$entityManager->persist ( $newKategori );
		$entityManager->flush ();

    	return array("success"=>true);
    }

    /**
     * @Route("/latihan/sendemail", name="latihansendemail")
     * @Method("GET")
     */
    public function latihanSendEmailAction() {
        $sendEmail=$this->kategoriService->sendEmail();
        print_r($sendEmail);
		exit();
		
        return $sendEmail;

    }

    /**
     * @Route("/latihan/jsonbiasa", name="latihanjsonbiasa")
     * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanJsonBiasaAction() {
        $result=array("success"=>true);
        return json_encode($result);
    }
    /**
     * @Route("/latihan/download", name="latihandownload")
     * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanDownloadAction() {
        $response = new Response();
        $file=dirname(dirname(__FILE__)).'/Resources/public/download/test.csv';
                
        if (file_exists($file)) {           
           $response->headers->set('Content-Description', 'File Transfer');
           $response->headers->set('Content-Type', 'text/csv');
           $response->headers->set('Content-Disposition','attachment; filename='.basename($file));
           $response->headers->set('Content-Transfer-Encoding', 'binary');
           $response->headers->set('Expires','0');
           $response->headers->set('Cache-Control','must-revalidate');
           $response->headers->set('Pragma','public');
           $response->headers->set('Content-Length' ,filesize($file));
           readfile($file);
           return $response;            
        } else {
            return array("level"=>"error",
                         "message"=>"File not found"
                        );
        }   
    }


    /**
     * @Route("/latihan/parsecsv", name="latihanparsecsv")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:parsecsv.html.twig")     
     */
    public function latihansavecsvAction(Request $request){
        //untuk mendapatkan info method apa yang digunakan
        $method=$request->getMethod();
        //menggunakan form bernama formFile
        $formElement= $this->formFactory->create("formFile");

        //penangan method post
        $formElement->handleRequest($request);

        if ($formElement->isValid()) {
            $dir=dirname(dirname(__FILE__)).'/Resources/public/csv/';
            $file=$formElement["file"]->getData();

            $mimeType=$file->getMimeType();
            
            $originalName=$file->getClientOriginalName();
            
            $ext=$file->getExtension();
            
            $size=$file->getSize();

            //ini untuk mendapatkan info error 
            $error=$file->getError();

            if (($handle = fopen($file, "r")) !== FALSE) {            
              $parsecsv=$this->bukuService->parseCsv($handle);
            }
            fclose($handle);

            echo " File has been uploaded";
            $file->move($dir,$originalName);
        }
        return array('formFile' => $formElement->createView());

    }

    /**
     * @Route("/latihan/element", name="latihan-element")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:latihanelement.html.twig")
     */
    public function latihanElementAction(Request $request) {
        $formElement= $this->formFactory->create("formelement");

        $formElement->handleRequest($request);

        if ($formElement->isValid()) {
            $dir=dirname(dirname(__FILE__)).'/Resources/public/images/';
            $file=$formElement["File"]->getData();
            $mimeType=$file->getMimeType();
            $originalName=$file->getClientOriginalName();
            $ext=$file->guessExtension();
            $size=$file->getSize();
            $error=$file->getError();
            echo " File has been uploaded";
            $file->move($dir,time().".".$ext);

        }
        return array('formElement' => $formElement->createView());
    }

    /**
     * @Route("/latihan/form", name="latihan-form")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:latihanform.html.twig")
     */
    public function latihanFormAction(Request $request) {
        $formKategori= $this->formFactory->create("formkategori");

        $formKategori->handleRequest($request);

        if ($formKategori->isValid()) {
        
            $namaKategori=$formKategori->get('namaKategori')->getData();
            $noRak=$formKategori->get('noRak')->getData();
            
            if($formKategori->get('FirstButton')->isClicked()){
                echo "First Button";
            }elseif($formKategori->get('SecondButton')->isClicked()){
                echo "Second Button";
            }

        }
        return array('formKategori' => $formKategori->createView());
    }

    
    /**
     * @Route("/latihan/getdata", name="latihangetdata")
     * @Method("GET")
     */
   public function latihangetdataAction(Request $request) {
        $response=new Response();                                
       $response->headers->set('Content-Description', 'File Transfer');
       $response->headers->set('Content-Type', 'text/html');

        $judul=$request->query->get("judul");
        echo " Judul Buku : ".$judul;
        return $response;
    }

    /**
     * @Route("/latihan/post", name="latihanpost")
     * @Method({"POST","GET"})
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:latihanpost.html.twig")
     */
   public function latihanpostAction(Request $request) {
        $errorMessage="";
        $method=$request->getMethod();
        $noRak="";
        $namaKategori="";

        if($method=="POST"){
            $namaKategori=$request->request->get("namaKategori");
            $noRak=$request->request->get("noRak");

            if(empty($namaKategori)){
                $errorMessage="Nama Kategori masih kosong";
            }elseif(empty($noRak)){
                $errorMessage="No rak masih kosong";
            }else{
                $message="Berhasil";
                echo $message;
            }
            
            if(empty($errorMessage)){
                return array("success"=>true,"message"=>$message,"noRak"=>$noRak,"namaKategori"=>$namaKategori);
            }else{

                return array("success"=>false,"errorMessage"=>$errorMessage,"namaKategori"=>$namaKategori,"noRak"=>$noRak);
            }
        }else{
            return array("success"=>true,"noRak"=>$noRak,"namaKategori"=>$namaKategori);
        }
    }

    /**
     * @Route("/latihan/buku/{judul}", name="latihangetdata")
     * @Method("GET")
     */
   public function latihangetdatatipeduaAction($judul=null) {
       $response=new Response();                                
       $response->headers->set('Content-Description', 'File Transfer');
       $response->headers->set('Content-Type', 'text/html');

        echo " Judul Buku : ".$judul;
        return $response;
    }

    /**
     * @Route("/latihan/tampil-kategori", name="latihan-tampilkategori")
	 * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:
     Latihan:latihan.html.twig")
     */
    public function latihanTampilKategoriAction() {

    	$entityManager=$this->entityManager;
        $repository=$entityManager->getRepository('PerpustakaanAnseraBundle:Kategori');
        $kategori=$repository->findAll();
        /*
        if(!empty($kategori) && !is_null($kategori)){
           $kategori->setNamaKategori('komik');
           $kategori->setNoRak(3);
           $entityManager->flush();
        }else{
            echo "Data tidak ada";
        }*/

		//$listKategori=$repository->find(1);    
		//\Doctrine\Common\Util\Debug::dump($kategori);

    	return array("success"=>true,"kategoriBuku"=>$kategori);
    }

}