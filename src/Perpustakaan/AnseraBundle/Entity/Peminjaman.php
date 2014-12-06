<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Perpustakaan\AnseraBundle\Repository\PeminjamanRepository") 
 * @ORM\Table(name="trx_peminjaman")
 */
class Peminjaman
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="date")
	 */
	protected $tanggal_pinjam;

	/**	 
     * @ORM\ManyToOne(targetEntity="Anggota", inversedBy="peminjaman")
	 */
	protected $anggota;

	/**	 
     * @ORM\ManyToOne(targetEntity="Petugas", inversedBy="peminjaman")
	 */
	protected $petugas;


	/**	 
     * @ORM\OneToMany(targetEntity="DetailPeminjaman", mappedBy="peminjaman")
	 */
	protected $detailPeminjaman;

	/**	 
     * @ORM\OneToMany(targetEntity="Pengembalian", mappedBy="peminjaman")
	 */
	protected $pengembalian;

		
	/* Getters & Setters */

	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function getTanggalKembali() {
		return $this->tanggal_kembali;
	}

	public function getTanggalPinjam() {
		return $this->tanggal_pinjam;
	}

	public function getPetugas(){
		return $this->petugas;
	}

	public function getAnggota() {
		return $this->anggota;
	}

	public function getDetailPengembalian(){
		return $this->detailPengembalian;
	}
	public function getDetailPeminjaman(){
		return $this->detailPeminjaman;
	}
	
	public function setTanggalPinjam() {
		$this->tanggal_pinjam= new \DateTime("now");
		return $this;
	}

	
	public function setAnggota(Anggota $anggota){
		$anggota->addPeminjaman($this);
		$this->anggota=$anggota;
		return $this;
	}

	public function setPetugas(Petugas $petugas){
		$petugas->addPeminjaman($this);
		$this->petugas=$petugas;
		return $this;
	}

	public function addDetailPengembalian(DetailPengembalian $detailPengembalian){
		$this->detailPengembalian[]=$detailPengembalian;
		return $this;
	}

	public function addPengembalian(Pengembalian $pengembalian){
		$this->pengembalian[]=$pengembalian;
		return $this;
	}

	public function addDetailPeminjaman(DetailPeminjaman $detailPeminjaman){
		$this->detailPeminjaman[]=$detailPeminjaman;
		return $this;
	}

	
	
}
