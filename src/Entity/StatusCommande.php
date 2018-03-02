<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * StatusCommande
 *
 * @ORM\Table(name="status_commande")
 * @ORM\Entity
 */
class StatusCommande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_statut", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idStatut;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * StatusCommande constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getIdStatut()
    {
        return $this->idStatut;
    }

    /**
     * @param int $idStatut
     */
    public function setIdStatut(int $idStatut)
    {
        $this->idStatut = $idStatut;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }


}
