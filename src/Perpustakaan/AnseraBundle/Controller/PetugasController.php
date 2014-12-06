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
use Perpustakaan\AnseraBundle\Service\PetugasService;


class PetugasController 
{
    /**
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    protected $entityManager;

    /**
     * @DI\Inject("perpus.service.petugas")
     */
    protected $petugasService;

    /**
     * @DI\Inject("session")
     */
    protected $session;

    /**
     * @DI\Inject("router")
     */
    protected $router;


    /**
     * @Route("/logout", name="logout")
     * @Method("GET")
     */
    public function logoutAction() {
        $this->session->remove("petugas");
            return new RedirectResponse($this->router->generate('login'));                    
        return array("success"=>true);
    }

    /**
     * @Route("/", name="slash")
     * @Method("GET")
     */
    public function slashAction() {
            return new RedirectResponse($this->router->generate('login'));                    
        return array("success"=>true);
    }

    /**
     * @Route("/login", name="login")
     * @Method({"GET","POST"})
     * @FOS\View("PerpustakaanAnseraBundle:Petugas:login.html.twig")
     */
    public function loginAction(Request $request) {
        $email=$request->request->get("email");
        $password=$request->request->get("password");
        $error="";
        $method=$request->getMethod();
        $session=$this->session->get("petugas");
        
        if(!empty($session) && !is_null($session)){
            return new RedirectResponse($this->router->generate('kategori'));                    
        }

        if($method=="POST"){
            if(empty($email)){
                $error="Email is empty";
            }elseif(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error="Invalid email";
            }elseif(empty($password)){
                $error="Password is emtpy";
            }else{
                $login=$this->petugasService->login($email,$password);
                if($login["level"]=="success"){
                    return new RedirectResponse($this->router->generate('kategori'));                    
                }else{
                    $error=$login["errMessage"];
                }
            }
        }
        if(!empty($error)){
            $result=array("success"=>false,"error"=>$error,"email"=>$email,"password"=>$password);
        }else{
            
            $result=array("success"=>true);
        }

        return $result;
    }

}