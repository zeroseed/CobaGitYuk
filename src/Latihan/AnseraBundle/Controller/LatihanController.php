<?php

namespace Latihan\AnseraBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use FOS\RestBundle\Controller\Annotations as FOS;

use Latihan\AnseraBundle\Form\FormArtist;
use Latihan\AnseraBundle\Dto\ArtistDto;
use Latihan\AnseraBundle\Form\FormBuah;

use Latihan\AnseraBundle\Service\LatihanService;
use Latihan\AnseraBundle\Entity\Artist;

use Latihan\AnseraBundle\Dto as Dto;

class LatihanController 
{

    /**
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    protected $entityManager;

    /**
     * @DI\Inject("lat.service.buah")
     */
    protected $buahService;

    /**
     * @DI\Inject("lat.service.sayuran")
     */
    protected $sayuranService;

    /**
     * @DI\Inject("form.factory")
     */
    protected $formFactory;
    /**
     * @DI\Inject("lat.service.latihan")
     */
    protected $latihanService;


   /**
     * @Route("/latihan/form/artist", name="latihan_form_artist")
	 * @Method({"GET","POST"})
     * @FOS\View("LatihanAnseraBundle:Latihan:index.html.twig")
     */
    public function latihanformartistAction(Request $request) {
        $formArtist= $this->formFactory->create("formArtist");
        $formArtist->handleRequest($request);
        if ($formArtist->isValid()) {
            $nama=$formArtist->get('nama')->getData();
            $pekerjaan=$formArtist->get('pekerjaan')->getData();
            
            echo "<h1> ini contoh submit data </h1>";
            echo " NAMA : ".$nama."<br/>";
            echo " PEKERJAAN : ".$pekerjaan;
        }

        return array('formArtist' => $formArtist->createView());

    }

    /**
     * @Route("/latihan/getdata", name="latihan_get_data")
     * @Method("GET")
     */
   public function latihangetdataAction(Request $request) {
        $response=new Response();                                
       $response->headers->set('Content-Description', 'File Transfer');
       $response->headers->set('Content-Type', 'text/html');

        $nama=$request->query->get("nama");
        echo " nama artist: ".$nama;
        return $response;
    }


    /**
     * @Route("/latihan/post", name="latihanpost")
     * @Method({"POST","GET"})
     * @FOS\View("LatihanAnseraBundle:Latihan:latihanpost.html.twig")
     */
   public function latihanpostAction(Request $request) {
        $errorMessage="";
        $method=$request->getMethod();
        $noRak="";
        $namaKategori="";

        if($method=="POST"){
            $nama=$request->request->get("nama");
            $pekerjaan=$request->request->get("pekerjaan");

            if(empty($nama)){
                $errorMessage="Nama masih kosong";
            }elseif(empty($pekerjaan)){
                $errorMessage="Pekerjaan masih kosong";
            }else{
                $message="Berhasil";
                echo $message;
            }
            
            if(empty($errorMessage)){
                return array("success"=>true,"message"=>$message,"nama"=>$nama,"pekerjaan"=>$pekerjaan);
            }else{

                return array("success"=>false,"errorMessage"=>$errorMessage,"nama"=>$nama,"pekerjaan"=>$pekerjaan);
            }
        }else{
            return array("success"=>false,"errorMessage"=>"Method bukan post");
        }
    }

    /**
     * @Route("/latihan/artist/{nama}", name="latihan_get_data_versi2")
     * @Method("GET")
     */
   public function latihangetdatatipe2Action(Request $request,$nama=null) {
        $response=new Response();                                
       $response->headers->set('Content-Description', 'File Transfer');
       $response->headers->set('Content-Type', 'text/html');

        $nama=$request->query->get("nama");
        echo " nama artist: ".$nama;
        return $response;
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
              $parsecsv=$this->latihanService->parseCsv($handle);
            }
            fclose($handle);

            echo " File has been uploaded";
            $file->move($dir,$originalName);
        }
        return array('formFile' => $formElement->createView());

    }

    /**
     * @Route("/latihan/formbuah", name="latihan-formbuah")
     * @Method({"GET","POST"})
     * @FOS\View("LatihanAnseraBundle:Latihan:latihanform.html.twig")
     */
    public function latihanFormBuahAction(Request $request) {
        $formBuah= $this->formFactory->create("formBuah");

        $formBuah->handleRequest($request);

        if ($formBuah->isValid()) {
        
            $nama=$formBuah->get('namabuah')->getData();
            
            if($formBuah->get('FirstButton')->isClicked()){
                echo "First Button";
            }elseif($formBuah->get('SecondButton')->isClicked()){
                echo "Second Button";
            }

        }
        return array('formBuah' => $formBuah->createView());
    }

   /**
     * @Route("/latihan/perkalian", name="latihan_perkalian")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:perkalian.html.twig")
     */
    public function perkalianAction() {
        $bil1=10;
        $bil2=20;
        $hasil=$this->latihanService->perkalian($bil1,$bil2);

        return array("success"=>true,"hasil"=>$hasil,"bil1"=>$bil1,"bil2"=>$bil2);
    }

    /**
     * @Route("/latihan/relasiservice", name="latihanservice")
     * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanServiceAction() {
        $entityManager=$this->entityManager;
        $buahService=$this->buahService->panggilSayuran();
        print_r($buahService);

        return array("success"=>true);
    }

   /**
     * @Route("/latihan/dto", name="latihan_pakai_dto")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:empty.html.twig")
     */
    public function dtoAction() {
        $artist=$this->entityManager->getRepository('LatihanAnseraBundle:Artist')->find(3);

        $artistDTO = DTO\ArtistDto::fromEntity($artist);
        \Doctrine\Common\Util\Debug::dump($artistDTO);

        return $artistDTO;
    }

    /**
     * @Route("/latihan/errorcode", name="latihanerrorcode")
     * @Method({"GET","POST"})
     */
    public function latihanErrorCodeAction(Request $request) {
            $result=array("success"=>true);
            $response = new Response(json_encode($result));
            $response->setStatusCode(500);

            return $response;
    }

    /**
     * @Route("/latihan/getjson.{_format}", name="latihan_getjson")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanGetJsonAction(Request $request) {
        $response=array("level"=>"success");
        return $response;

    }


    /**
     * @Route("/latihan/sendemail", name="latihansendemail")
     * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanSendEmailAction() {
        $sendEmail=$this->latihanService->sendEmail();
        print_r($sendEmail);
        return $sendEmail;

    }

    /**
     * @Route("/latihan/simpan/artist", name="latihan_simpanartist")
     * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanSimpanArtistAction() {
        $entityManager=$this->entityManager;

        $newArtist=new Artist ();
        $newArtist->setNama('Sherina Munaf');
        $newArtist->setPekerjaan('Musisi');
        
        $entityManager->persist ( $newArtist );

        $entityManager->flush ();
        echo " Data sudah disimpan ";
        return array("success"=>true);
    }

    /**
     *@Route("/latihan/simpan-artist", name="latihansimpanartist")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanAction() {
        $entityManager=$this->entityManager;

        $newArtist=new Artist ();
        $newArtist->setNama ('Giring Nidji');
        $newArtist->setEmail('test.giring@gmail.com');
        
        $entityManager->persist ( $newArtist );
        $entityManager->flush ();
        echo "Data sudah disimpan";
        return array("success"=>true);
    }

    /**
     *@Route("/latihan/orderby", name="latihanorderby")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:findby.html.twig")
     */
    public function latihanOrderByAction() {
        $entityManager=$this->entityManager;
        $repo=$entityManager->getRepository('LatihanAnseraBundle:Artist');
        $listArtist=$repo->findBy(array(),array("nama"=>"DESC"));
 
        return array("success"=>true,"listArtist"=>$listArtist);
 
    }

    /**
     *@Route("/latihan/findall", name="latihanfindall")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:findall.html.twig")
     */
    public function latihanFindAllAction() {
        $entityManager=$this->entityManager;
        $repo=$entityManager->getRepository('LatihanAnseraBundle:Artist');

        $listArtist=$repo->findAll();
 
        return array("success"=>true,"listArtist"=>$listArtist);
    }

    /**
     *@Route("/latihan/findBy", name="latihanfindBy")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:findBy.html.twig")
     */
    public function latihanFindByAction() {

        $entityManager=$this->entityManager;
        $repo=$entityManager->getRepository('LatihanAnseraBundle:Artist');
        $listArtist=$repo->findBy(array('nama'=>'Sherina Munaf'));

        //$listArtist=$repo->findByNama('Sherina Munaf');

        return array("success"=>true,"listArtist"=>$listArtist);
    }

    /**
     *@Route("/latihan/update", name="latihanupdate")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanUpdateAction() {
        $entityManager=$this->entityManager;
        $repository=$entityManager->getRepository('LatihanAnseraBundle:Artist');
        $artist=$repository->find(1);
    
        if(!empty($artist) && !is_null($artist)){
            $artist->setNama ('Sherina');
            $artist->setPekerjaan ('Pemain Film');

            echo "Data sudah diubah";
        }else{
            echo "Data tidak ada";
        }
    }

    /**
     *@Route("/latihan/find", name="latihanfind")
     * @Method("GET")
     * @FOS\View("LatihanAnseraBundle:Latihan:find.html.twig")
     */
    public function latihanFindAction() {

        $entityManager=$this->entityManager;
        $repo=$entityManager->getRepository('LatihanAnseraBundle:Artist');

        $artist=$repo->find(5);

        return array("success"=>true,"artist"=>$artist);
    }

    /**
     * @Route("/latihan/tampil/repository", name="latihantampilrepository")
     * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanTampilRepositoryAction() {
        $artistEntity=$this->entityManager->getRepository('LatihanAnseraBundle:Artist');
        $artistRepo=$artistEntity->getQueryArtist();
        print_r($artistRepo);

        return array("success"=>true);
    }

    /**
     * @Route("/latihan/tampil/randomhash", name="latihantampiltampilrandom")
     * @Method("GET")
     * @FOS\View("PerpustakaanAnseraBundle:Latihan:empty.html.twig")
     */
    public function latihanTampilRandomAction() {
        $randomHash=$this->latihanService->getRandomHash();
        print_r($randomHash);

        return array("success"=>true);
    }

}