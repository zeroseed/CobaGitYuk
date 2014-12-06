<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="anggota")
 */
class Anggota
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string",length=100)
	 */
	protected $nama;


	/**
	 * @ORM\Column(type="date",name="tanggal_bergabung")
  	 * @var \DateTime
	 */
	protected $tanggal_bergabung;

	/**
	 * @ORM\Column(type="integer",name="no_telepon")
	 */
	protected $no_telepon;

	/**
	 * @ORM\Column(type="text",name="alamat")
	 */
	protected $alamat;

	/**	 
     * @ORM\OneToMany(targetEntity="Peminjaman", mappedBy="anggota")
	 */
	protected $peminjaman;

	/**	 
     * @ORM\OneToMany(targetEntity="Pengembalian", mappedBy="anggota")
	 */
	protected $pengembalian;
		
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

	public function getNoTelepon() {
		return $this->no_telepon;
	}

	public function getAlamat() {
		return $this->alamat;
	}

	public function getPengembalian() {
		return $this->pengembalian;
	}

	public function getPeminjaman(){
		return $this->peminjaman;
	}

	public function getTanggalBergabung() {
		return $this->tanggal_bergabung;
	}

	public function addPeminjaman(Peminjaman $peminjaman){
		$this->peminjaman->add($peminjaman);
		return $this;
	}
	
	public function setNama($nama) {
		$this->nama = $nama;
		return $this;
	}

	public function setTanggalBergabung() {
		$this->tanggal_bergabung = new \DateTime("now");

		return $this;
	}
	
	public function setNoTelepon($no_telepon){
		$this->no_telepon=$no_telepon;
		return $this;
	}
	
	public function setAlamat($alamat){
		$this->alamat=$alamat;
		return $this;
	}

	public function addPengembalian(Pengembalian $pengembalian){
		$this->pengembalian->add($pengembalian);

		return $this;
	}

}
