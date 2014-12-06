<?php

namespace Perpustakaan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="petugas")
 */
class Petugas
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
	 * @ORM\Column(type="string",name="email")
	 */
	protected $email;
		
	/**
	 * @ORM\Column(type="string")
	 */
	protected $password;

	/**	 
     * @ORM\OneToMany(targetEntity="Peminjaman", mappedBy="buku")
	 */
	protected $peminjaman;

	/**	 
     * @ORM\OneToMany(targetEntity="Penerimaan", mappedBy="petugas")
	 */
	protected $penerimaan;

	/**	 
     * @ORM\OneToMany(targetEntity="Pengembalian", mappedBy="petugas")
	 */
	protected $pengembalian;

	/* Getters & Setters */

	public function __construct(){
       $this->penerimaan= new ArrayCollection();
       $this->peminjaman= new ArrayCollection();
       $this->pengembalian= new ArrayCollection();

	}
	/**
	 * Return the Id
	 */
	public function getId() {
		return $this->id;
	}

	public function setNama($nama){
		$this->nama=$nama;
		return $this;
	}

	public function setEmail($email){
		$this->email=$email;
		return $this;
	}

	public function setPassowrd($password){
		$this->password=$password;
		return $this;
	}

	public function addPeminjaman(Peminjaman $peminjaman){
		$this->peminjaman[]=$peminjaman;
	}

	public function addPenerimaan(Penerimaan $penerimaan){
		$this->penerimaan[]=$penerimaan;
	}
	
	public function addPengembalian(Pengembalian $pengembalian){
		$this->pengembalian[]=$pengembalian;
	}
	
    public function getPassword(){
    	return $this->password;
    }

    public function getEmail(){
    	return $this->email;
    }
	
	public function getNama(){
		return $this->nama;

	}

	public function getPeminjaman(){
		return $this->peminjaman;
	}
	
	public function getPenerimaan(){
		return $this->penerimaan;
	}

	public function getPengembalian(){
		return $this->pengembalian;
	}

}
