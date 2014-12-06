<?php

namespace Perpustakaan\AnseraBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Monolog\Logger;

use Perpustakaan\AnseraBundle\Entity\Petugas;

/**
 * @DI\Service("perpus.service.petugas")
 * @DI\Tag("security.secure_service")
 */
class PetugasService
{

    protected $entityManager;

    protected $passwordEncoder;

    protected $session;

    /**
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "passwordEncoder" = @DI\Inject("security.encoder.blowfish"),
   	 * 	   "session" = @DI\Inject("session"),
	 *  })
     */
    public function __construct(EntityManager $entityManager,BasePasswordEncoder $passwordEncoder,$session) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session=$session;
        
    }

    protected function _getPetugasRepository() {
        return $this->entityManager->getRepository('PerpustakaanAnseraBundle:Petugas');
    }

    public function sendEmail(){
                
        $email=array("ansera.test@gmail.com");
        
        $subject="Test email";
        $body ="<h1> Test Kirim Email </h1>";
        $body .= " Hi! This is email from localhost";
        
        $mail=$this->mailer->sendTemplatelessEmail($email,$subject,$body);
        return true;                
    }
    public function login($email,$password){
    	$errorMessage="";
    	$Petugas=$this->_getPetugasRepository()->findOneBy(array("email"=>$email));
		if(!empty($Petugas) && !is_null($Petugas)){
			$isValid = $this->passwordEncoder->isPasswordValid(
			            $Petugas->getPassword(), $password, null
			        	);	
			if(!$isValid){
				$errorMessage="Wrong password  Please try again";
			}else{
				$PetugasId=$Petugas->getId();
				$this->setApplicationSession($PetugasId);				
			}
		}else{
			$errorMessage="Email not found in our system.";
		}
		
		if(!empty($errorMessage)){
			$result=array("level"=>"error","errMessage"=>$errorMessage);
		}else{
			$result=array("level"=>"success");
		}
		
		return $result;
    }

    public function setApplicationSession($petugasId){
    	$petugas=$this->_getPetugasRepository()->find($petugasId);
    	$sessionPetugas=array("id"=>$petugasId,"nama"=>$petugas->getNama(),"email"=>$petugas->getEmail());
    	$this->session->set("petugas",$sessionPetugas);
    	return true;
    }
}
