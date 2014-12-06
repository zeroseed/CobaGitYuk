<?php

namespace Latihan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="guru")
 */
class Guru
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string",name="nama",length=100)
	 */
	protected $nama;

	/**
	 * @ORM\Column(type="text",name="alamat")
	 */
	protected $alamat;
		

	/**	 
     * @ORM\OneToOne(targetEntity="Kelas", inversedBy="guru")
	 */
	protected $kelas;
		

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

	public function getAlamat() {
		return $this->noRak;
	}

	public function getKelas() {
		return $this->kelas;
	}

	public function setNama($nama) {
		$this->nama = $nama;
		return $this;
	}

	public function addKelas(Kelas $kelas){
		$this->kelas->add($this);
		return $this;
	}


	public function setAlamat($alamat) {
		$this->alamat = $alamat;
		return $this;
	}
	
	public function setKelas(Kelas $kelas) {
		$kelas->addGuru($this);
		$this->kelas=$kelas;
		return $this;
	}
	

}
