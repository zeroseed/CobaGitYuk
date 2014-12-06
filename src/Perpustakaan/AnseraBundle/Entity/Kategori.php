<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Perpustakaan\AnseraBundle\Repository\KategoriRepository")
 * @ORM\Table(name="kategori")
 */
class Kategori
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string",name="nama_kategori",length=100)
	 */
	protected $namaKategori;


	/**
	 * @ORM\OneToMany(targetEntity="Buku",mappedBy="kategori",cascade={"persist"})
	 */
	protected $buku;

	/**
	 * @ORM\Column(type="integer",name="no_rak")
	 */
	protected $noRak;
		
	/* Getters & Setters */

	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function getNamaKategori() {
		return $this->namaKategori;
	}

	public function getNoRak() {
		return $this->noRak;
	}

	public function getBuku() {
		return $this->buku;
	}

	public function addBuku(Buku $buku){
		$this->buku->add($buku);
	}

	public function setNamaKategori($namaKategori) {
		$this->namaKategori = $namaKategori;
		return $this;
	}

	public function setNoRak($noRak) {
		$this->noRak = $noRak;
		return $this;
	}
	
	public function __toString() {
	    return $this->namaKategori;
	}


}
