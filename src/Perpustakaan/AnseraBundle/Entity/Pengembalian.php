<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Perpustakaan\AnseraBundle\Repository\PengembalianRepository")  
 * @ORM\Table(name="trx_pengembalian")
 */
class Pengembalian
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**	 
     * @ORM\ManyToOne(targetEntity="Anggota", inversedBy="pengembalian")
	 */
	protected $anggota;

	/**	 
     * @ORM\ManyToOne(targetEntity="Peminjaman", inversedBy="pengembalian")
	 */
	protected $peminjaman;

	/**	 
     * @ORM\ManyToOne(targetEntity="Petugas", inversedBy="pengembalian")
	 */
	protected $petugas;

	/**	 
     * @ORM\OneToMany(targetEntity="DetailPengembalian", mappedBy="pengembalian")
	 */
	protected $detailPengembalian;
		
	/* Getters & Setters */

	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function getAnggota() {
		return $this->anggota;
	}

	public function getPetugas() {
		return $this->petugas;
	}

	public function getDetailPengembalian(){
		return $this->detailPengembalian;
	}

	public function setPeminjaman(Peminjaman $peminjaman){
		$peminjaman->addPengembalian($this);
		$this->peminjaman=$peminjaman;
		return $this;
	}
	public function setAnggota(Anggota $anggota){
		$anggota->addPengembalian($this);
		$this->anggota=$anggota;
		return $this;
	}

	public function setPetugas(Petugas $petugas){
		$petugas->addPengembalian($this);
		$this->petugas=$petugas;
		return $this;
	}

	public function addDetailPengembalian(DetailPengembalian $detailPengembalian){
		$this->detailPengembalian[]=$detailPengembalian;
		return $this;
	}		
}
