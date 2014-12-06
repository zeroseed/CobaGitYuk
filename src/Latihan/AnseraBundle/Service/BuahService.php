<?php

namespace Latihan\AnseraBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Latihan\AnseraBundle\Utility\HashGenerator;

/**
 * @DI\Service("lat.service.buah")
 * @DI\Tag("security.secure_service")
 */
class BuahService
{

	protected $sayuranService;

    /**
     * @DI\InjectParams({
     * "sayuranService"=@DI\Inject("lat.service.sayuran")
     * })
     */
    public function __construct(SayuranService $sayuranService) {
		$this->sayuranService=$sayuranService;	
    }

    public function panggilSayuran(){
    	$sayuran=$this->sayuranService->getSayuran();
    	return $sayuran;
    }


}