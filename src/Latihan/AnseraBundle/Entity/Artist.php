<?php

namespace Latihan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="artist")
 * @ORM\Entity(repositoryClass="Latihan\AnseraBundle\Repository\ArtistRepository")
 */
class Artist
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string",length=25,nullable=true)
	 */
	protected $nama;

	/**
	 * @ORM\Column(type="string", length=25,name="pekerjaan")
	 */
	protected $pekerjaan;

		
	/* Getters & Setters */

	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function getNama() {
		return $this->nama;
	}

	public function getPekerjaan(){
		return $this->pekerjaan;
	}

	public function setNama($nama) {
		$this->nama = $nama;
		return $this;
	}

	public function setPekerjaan($pekerjaan) {
		$this->pekerjaan = $pekerjaan;
		return $this;
	}


}
