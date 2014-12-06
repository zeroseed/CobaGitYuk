<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="trx_detail_peminjaman")
 */
class DetailPeminjaman
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;


	/**	 
     * @ORM\ManyToOne(targetEntity="Buku", inversedBy="peminjaman")
	 */
	protected $buku;

	/**
	 * @ORM\Column(type="date",nullable=true)
	 * @var \DateTime
	 */
	protected $tanggal_kembali;

	/**
	 * @ORM\Column(type="integer",nullable=true)
	 */
	protected $terlambat;

	/**	 
     * @ORM\ManyToOne(targetEntity="Peminjaman", inversedBy="peminjaman",cascade={"persist"})
	 */
	protected $peminjaman;

	/**
	 * @ORM\Column(type="integer",name="denda",nullable=true)
	 */
	protected $denda;
		
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

	public function getBuku() {
		return $this->buku;
	}

	public function getDenda() {
		return $this->denda;
	}

	public function getPeminjaman(){
		return $this->peminjaman;
	}

	public function getTerlambat(){
		return $this->terlambat;
	}
	
	public function setTerlambat($terlambat){
		$this->terlambat=$terlambat;
		return $this;
	}
	public function setDenda($denda){
		$this->denda=$denda;
		return $this;
	}
	public function setTanggalKembali($tanggal_kembali) {

		$this->tanggal_kembali= new \DateTime($tanggal_kembali);
		return $this;
	}

	public function setPeminjaman(Peminjaman $peminjaman){
		$peminjaman->addDetailPeminjaman($this);
		$this->peminjaman=$peminjaman;
		return $this;
	}

	public function setBuku(Buku $buku){
		$buku->addDetailPeminjaman($this);
		$this->buku=$buku;
		return $this;
	}

}
