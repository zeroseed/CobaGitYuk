<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="trx_detail_penerimaan")
 */
class DetailPenerimaan
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
 	 * @var \DateTime
	 */
	protected $tanggal;


	/**
	 * @ORM\ManyToOne(targetEntity="Penerimaan",inversedBy="detailPenerimaan",cascade={"persist"})
	 */
	protected $penerimaan;

	/**
	 * @ORM\ManyToOne(targetEntity="Buku",inversedBy="detailPenerimaan")
	 */
	protected $buku;

	/**
	 * @ORM\Column(type="integer",name="total",nullable=true)
	 */
	protected $total;
		
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

	public function getTotal() {
		return $this->total;
	}

	public function getPetugas() {
		return $this->petugas;
	}

	public function getBuku(){
		return $this->buku;
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

	public function setBuku(Buku $buku){
		$buku->addDetailPenerimaan($this);
		$this->buku=$buku;
		return $this;
	}

	public function setPenerimaan(Penerimaan $penerimaan){
		$penerimaan->addDetailPenerimaan($this);
		$this->penerimaan=$penerimaan;
		return $this;
	}

	public function setTotal($total) {
		$this->total = $total;
		return $this;
	}
	

}
