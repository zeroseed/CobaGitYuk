<?php

namespace Latihan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="kelas")
 */
class Kelas
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string",name="nama",length=5)
	 */
	protected $nama;


	/**
	 * @ORM\OneToOne(targetEntity="Guru", mappedBy="kelas" )
	 */
	protected $guru;
		
	/* Getters & Setters */

	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function getNama() {
		return $this->namaKategori;
	}

	public function getNoRak() {
		return $this->noRak;
	}


	public function setNamaKategori($namaKategori) {
		$this->namaKategori = $namaKategori;
		return $this;
	}

	public function setNoRak($noRak) {
		$this->noRak = $noRak;
		return $this;
	}

	public function addGuru(Guru $guru){
		$this->guru->add($guru);
		return $this;
	}
	
	public function setGuru(Guru $guru) {
		$guru->addKelas($this);
		$this->guru=$guru;
		return $this;
	}


}
