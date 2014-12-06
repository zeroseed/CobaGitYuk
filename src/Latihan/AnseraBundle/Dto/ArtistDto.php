<?php

namespace Latihan\AnseraBundle\Dto;
use Symfony\Component\Validator\Constraints as Assert;
use Latihan\AnseraBundle\Entity\Artist;

class ArtistDto
{

    /**
	 * @Assert\NotBlank()
     */
	protected $id;

    /**
	 * @Assert\NotBlank()
     */
	protected $nama;

    /**
	 * @Assert\NotBlank()
     */
	protected $pekerjaan;

    public function setId($id) {
        $this->id = $id;
    }


    public function setNama($nama) {
        $this->nama = $nama;
    }

	public function setPekerjaan($pekerjaan) {
        $this->pekerjaan = $pekerjaan;
    }

    public function getId() {
        return $this->id;
    }

    public function getNama() {
        return $this->nama;
    }

    public function getPekerjaan() {
        return $this->pekerjaan;
    }

    public static function fromEntity(Artist $artist) {
        $artistDTO = new static();			
        $artistDTO->setId($artist->getId());        
        $artistDTO->setNama($artist->getNama());
        $artistDTO->setPekerjaan($artist->getPekerjaan());
        return $artistDTO;
    }


}