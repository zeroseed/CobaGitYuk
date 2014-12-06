<?php

namespace Latihan\AnseraBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="mahasiswa")
 */
class Mahasiswa
{
	/* Entity Field Definitions */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string",name="nama",length=100)
	 */
	protected $nama;

		
	/**
	 * @ORM\Column(type="string",name="jurusan")
	 */
	protected $jurusan;

	/**	 
     * @ORM\Column(type="string")
	 */
	protected $kelas;

	/**
	 * @ORM\ManyToMany(targetEntity="Matakuliah",inversedBy="mahasiswa")
	 */
	protected $mataKuliah;

}
