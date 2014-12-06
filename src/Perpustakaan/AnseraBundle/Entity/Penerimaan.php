<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="trx_penerimaan")
 */
class Penerimaan
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
	 * @ORM\ManyToOne(targetEntity="Petugas",inversedBy="penerimaan")
	 */
	protected $petugas;

	/**
	 * @ORM\OneToMany(targetEntity="DetailPenerimaan",mappedBy="penerimaan")
	 */
	protected $detailPenerimaan;

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

	public function setPetugas(Petugas $petugas){
		$petugas->addPenerimaan($this);
		$this->petugas=$petugas;
		return $this;
	}

	public function setTanggal() {

		$this->tanggal =new \DateTime("now");
		return $this;
	}

	public function addDetailPenerimaan(DetailPenerimaan $detailPenerimaan){
		$this->detailPenerimaan[]=$detailPenerimaan;
		return $this;
	}

	public function setTotal($total) {
		$this->total = $total;
		return $this;
	}
	

}
