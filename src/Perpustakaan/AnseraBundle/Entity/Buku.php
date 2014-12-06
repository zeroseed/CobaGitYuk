<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Perpustakaan\AnseraBundle\Repository\BukuRepository") 
 * @ORM\Table(name="buku")
 */
class Buku
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
	protected $judul;

	/**
	 * @ORM\Column(type="string",name="penulis")
	 */
	protected $penulis;

	/**
	 * @ORM\Column(type="integer",name="tahun_terbit")
	 */
	protected $tahunTerbit;

	/**
	 * @ORM\Column(type="string",name="penerbit")
	 */
	protected $penerbit;
		
	/**
	 * @ORM\Column(type="string",name="isbn")
	 */
	protected $isbn;

	/**
	 * @ORM\Column(type="integer",name="rusak_hilang",nullable=true)
	 */
	protected $rusakHilang;

	/**	 
     * @ORM\ManyToOne(targetEntity="Kategori", inversedBy="buku",cascade={"persist"})
	 */
	protected $kategori;

	/**	 
     * @ORM\OneToMany(targetEntity="DetailPeminjaman",mappedBy="buku")
	 */
	protected $detailPeminjaman;

	/**	 
     * @ORM\OneToMany(targetEntity="DetailPengembalian",mappedBy="buku")
	 */
	protected $detailPengembalian;

	/**	 
     * @ORM\OneToMany(targetEntity="DetailPenerimaan",mappedBy="buku")
	 */
	protected $detailPenerimaan;


	/**
	 * @ORM\Column(type="integer",name="total",nullable=true)
	 */
	protected $total;

	/**
	 * @ORM\Column(type="integer",name="total_keluar",nullable=true)
	 */
	protected $totalKeluar;

	/**
	 * @ORM\Column(type="integer",name="total_tersedia",nullable=true)
	 */
	protected $totalTersedia;


	/* Getters & Setters */

	public function __construct(){
	        $this->detailPenerimaan = new ArrayCollection();
	        $this->detailPengembalian = new ArrayCollection();
	        $this->detailPeminjaman = new ArrayCollection();

	}
	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function getJudul() {
		return $this->judul;
	}

	public function getTotalTersedia() {
		return $this->totalTersedia;
	}

	public function getTotalKeluar() {
		return $this->totalKeluar;
	}

	public function getPenulis() {
		return $this->penulis;
	}

	public function getPenerbit() {
		return $this->penerbit;
	}

	public function getIsbn() {
		return $this->isbn;
	}

	public function getRusakHilang() {
		return $this->rusakHilang;
	}


	public function getDetailPeminjaman(){
		return $this->detailPeminjaman;
	}
	public function getDetailPengembalian() {
		return $this->detailPengembalian;
	}

	public function getTahunTerbit() {
		return $this->tahunTerbit;
	}

	public function getKategori() {
		return $this->kategori;
	}

	public function getTotal() {
		return $this->total;
	}

	public function setJudul($judul) {
		$this->judul = $judul;
		return $this;
	}

	public function setRusakHilang($rusakHilang) {
		$this->rusakHilang=$rusakHilang;
		return $this;
	}

	public function setTotalTersedia($totalTersedia) {
		return $this->totalTersedia=$totalTersedia;
	}


	public function setTotalKeluar($totalKeluar) {
		return $this->totalKeluar=$totalKeluar;
	}

	public function setPenerbit($penerbit) {
		$this->penerbit = $penerbit;
		return $this;
	}
	
	public function setPenulis($penulis){
		$this->penulis=$penulis;
		return $this;
	}
	
	public function setTahunTerbit($tahunTerbit){
		$this->tahunTerbit=$tahunTerbit;
		return $this;
	}
	
	public function addDetailPengembalian(DetailPengembalian $detailPengembalian){
		$this->detailPengembalian[]=$detailPengembalian;
		return $this;
	}

	public function addDetailPenerimaan(DetailPenerimaan $detailPenerimaan){
		$this->detailPenerimaan[]=$detailPenerimaan;
		return $this;

	}
	public function setKategori(Kategori $kategori){
		$kategori->addBuku($this);
		$this->kategori=$kategori;
		return $this;
	}

	public function setTotal($total){
		$this->total=$total;
		return $this;
	}

	public function setIsbn($isbn){
		$this->isbn=$isbn;
		return $this;
	}

	public function addDetailPeminjaman(DetailPeminjaman $detailPeminjaman){
		$this->detailPeminjaman[]=$detailPeminjaman;
		return $this;
	}
	

}
