<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande", indexes={@ORM\Index(name="id_statut", columns={"id_statut"})})
 * @ORM\Entity
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_commande", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCommande;

    /**
     * @var int
     *
     * @ORM\Column(name="id_client", type="integer", nullable=false)
     */
    private $idClient;

    /**
     * @var \StatusCommande
     *
     * @ORM\ManyToOne(targetEntity="StatusCommande", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_statut", referencedColumnName="id_statut", onDelete="cascade")
     * })
     */
    private $idStatut;

    /**
     * Commande constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getIdCommande()
    {
        return $this->idCommande;
    }

    /**
     * @param int $idCommande
     */
    public function setIdCommande(int $idCommande)
    {
        $this->idCommande = $idCommande;
    }

    /**
     * @return int
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * @param int $idClient
     */
    public function setIdClient(int $idClient)
    {
        $this->idClient = $idClient;
    }

    /**
     * @return \StatusCommande
     */
    public function getIdStatut()
    {
        return $this->idStatut;
    }

    /**
     * @param \StatusCommande $idStatut
     */
    public function setIdStatut(\StatusCommande $idStatut)
    {
        $this->idStatut = $idStatut;
    }


}
