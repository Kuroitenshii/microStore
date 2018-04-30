<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"})
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="id_client", referencedColumnName="id")
     * })
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
     * @var float
     *
     * @ORM\Column(name="prix_commande", type="float", nullable=false)
     */
    private $prixCommande;

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
     * @return \User
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * @param User $idClient
     */
    public function setIdClient(User $idClient)
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
     * @param StatusCommande $idStatut
     */
    public function setIdStatut(StatusCommande $idStatut)
    {
        $this->idStatut = $idStatut;
    }

    /**
     * @return float
     */
    public function getPrixCommande()
    {
        return $this->prixCommande;
    }

    /**
     * @param float $prixCommande
     */
    public function setPrixCommande(float $prixCommande)
    {
        $this->prixCommande = $prixCommande;
    }

    public function serialize(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        return $serializer->serialize(array(
            'Numéro' => $this->getIdCommande(),
            'Prix' => $this->getPrixCommande(),
            'Statut' => $this->getIdStatut()->getNom(),
        ), 'json');
    }



}
