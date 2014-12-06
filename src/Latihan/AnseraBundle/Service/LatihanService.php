<?php

namespace Latihan\AnseraBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Latihan\AnseraBundle\Utility\HashGenerator;
use Latihan\AnseraBundle\Entity\Artist;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

/**
 * @DI\Service("lat.service.latihan")
 * @DI\Tag("security.secure_service")
 */
class LatihanService
{
    protected $hashGenerator;
    protected $mailer;
    protected $entityManager;

    /**
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
	 *     "hashGenerator" = @DI\Inject("lat.utility.hash_generator"),
     *     "mailer" = @DI\Inject("lat.service.mailer")	 
     * })
     */
    public function __construct(EntityManager $entityManager,HashGenerator $hashGenerator,MailerService $mailer) {
        $this->entityManager=$entityManager;
		$this->hashGenerator = $hashGenerator;
   		$this->mailer = $mailer;

		
    }

    public function parseCSV($fileHandle){
        $row=0;
        while (($data = fgetcsv($fileHandle, 1000, ",")) !== FALSE) {
                        
            //First line is for heading, skip it
            $row++;
            if($row==1) {
                continue;
            }

            if(!empty($data[0])){
                
                $newArtist=new Artist();
                
                $nama=$data[0];
                $pekerjaan=$data[1];

                $newArtist->setNama($nama);
                $newArtist->setPekerjaan($pekerjaan);
                $this->entityManager->persist($newArtist);
                $this->entityManager->flush();
            }
        }

        return array("level"=>"success");
    }

    public function sendEmail(){
                
        $email=array("ansera.test@gmail.com");
        
        $subject="Test email";
        $body ="<h1> Test Kirim Email </h1>";
        $body .= " Hi! This is email from localhost";
        
        $mail=$this->mailer->sendTemplatelessEmail($email,$subject,$body);
        return true;                
    }

	public function perkalian($bil1,$bil2){
		$hasil=$bil1 * $bil2;
		return $hasil;
	}

	public function getRandomHash(){
		  return $this->hashGenerator->generateRandomHash();
	}
}