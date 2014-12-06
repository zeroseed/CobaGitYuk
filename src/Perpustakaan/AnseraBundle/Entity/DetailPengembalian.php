<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="trx_detail_pengembalian")
 */
class DetailPengembalian
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="date",name="tanggal")
	 */
	protected $tanggal;


	/**
	 * @ORM\ManyToOne(targetEntity="Pengembalian",inversedBy="detailPengembalian",cascade={"persist"})
	 */
	protected $pengembalian;

	/**
	 * @ORM\ManyToOne(targetEntity="Peminjaman",inversedBy="detailPengembalian")
	 */
	protected $peminjaman;

	/**
	 * @ORM\ManyToOne(targetEntity="Buku",inversedBy="detailPengembalian")
	 */
	protected $buku;

	/**
	 * @ORM\Column(type="string",nullable=true)
	 */
	protected $status;
		
	/* Getters & Setters */

	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function getTanggal() {
		return $this->tanggal;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getTotal() {
		return $this->total;
	}

	public function getPetugas() {
		return $this->petugas;
	}

	public function getPeminjaman(){
		return $this->peminjaman;
	}
	public function getPenerimaan(){
		return $this->penerimaan;
	}

	public function setPetugas(Petugas $petugas){
		$petugas->addPenerimaan($this);
		$this->petugas=$petugas;
		return $this;
	}

	public function setTanggal() {
		$this->tanggal = new \DateTime("now");
		return $this;
	}

	public function setStatus($status){
		$this->status=$status;
		return $this;
	}

	public function setPenerimaan(Penerimaan $penerimaan){
		$penerimaan->addDetailPengembalian($this);
		$this->penerimaan=$penerimaan;
		return $this;
	}

	public function setBuku(Buku $buku){
		$buku->addDetailPengembalian($this);
		$this->buku=$buku;
		return $this;
	}

	public function setPeminjaman(Peminjaman $peminjaman){
		$peminjaman->addDetailPengembalian($this);
		$this->peminjaman=$peminjaman;
		return $this;
	}
	public function setPengembalian(Pengembalian $pengembalian){
		$pengembalian->addDetailPengembalian($this);
		$this->pengembalian=$pengembalian;
		return $this;
	}

	public function setTotal($total) {
		$this->total = $total;
		return $this;
	}
	

}
