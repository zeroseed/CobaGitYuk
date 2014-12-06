<?php

namespace Perpustakaan\AnseraBundle\Dto;
use Symfony\Component\Validator\Constraints as Assert;
use Perpustakaan\AnseraBundle\Entity\Kategori;

class KategoriDto
{

    /**
	 * @Assert\NotBlank()
     */
	protected $id;

    /**
	 * @Assert\NotBlank()
     */
	protected $noRak;

    /**
	 * @Assert\NotBlank()
     */
	protected $namaKategori;

    public function setId($id) {
        $this->id = $id;
    }


    public function setNoRak($noRak) {
        $this->noRak = $noRak;
    }

	public function setNamaKategori($namaKategori) {
        $this->namaKategori = $namaKategori;
    }

    public function getId() {
        return $this->id;
    }

    public function getNoRak() {
        return $this->noRak;
    }

    public function getNamaKategori() {
        return $this->namaKategori;
    }

    public static function fromEntity(Kategori $kategori) {
        $kategoriDTO = new static();			
        $kategoriDTO->setId($kategori->getId());        
        $kategoriDTO->setNoRak($kategori->getNoRak());
        $kategoriDTO->setNamaKategori($kategori->getNamaKategori());
        return $kategoriDTO;
    }


}