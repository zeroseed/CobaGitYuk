<?php

namespace Latihan\AnseraBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @DI\Service("lat.service.sayuran")
 * @DI\Tag("security.secure_service")
 */
class SayuranService
{

    /**
     * @DI\InjectParams({
     * "buahService"=@DI\Inject("lat.service.buah")
     * })
     */

	public function getSayuran(){
		$sayuran=array("wortel","bayam","kol");
		return $sayuran;
	}

}