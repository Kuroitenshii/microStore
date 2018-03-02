<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Panier
 *
 * @ORM\Table(name="panier", indexes={@ORM\Index(name="refPanier", columns={"ref_produit"})})
 * @ORM\Entity
 */
class Panier
{
    /**
     * @var \User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_client", referencedColumnName="id", onDelete="cascade")
     * })
     */
    private $idClient;

    /**
     * @var \Produits
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Produits", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_produit", referencedColumnName="ref", onDelete="cascade")
     * })
     */
    private $refProduit;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite_produit", type="integer", nullable=false)
     */
    private $quantiteProduit;

    /**
     * Panier constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return \User
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * @param \User $idClient
     */
    public function setIdClient(\User $idClient)
    {
        $this->idClient = $idClient;
    }

    /**
     * @return \Produits
     */
    public function getRefProduit()
    {
        return $this->refProduit;
    }

    /**
     * @param \Produits $refProduit
     */
    public function setRefProduit(\Produits $refProduit)
    {
        $this->refProduit = $refProduit;
    }

    /**
     * @return int
     */
    public function getQuantiteProduit()
    {
        return $this->quantiteProduit;
    }

    /**
     * @param int $quantiteProduit
     */
    public function setQuantiteProduit(int $quantiteProduit)
    {
        $this->quantiteProduit = $quantiteProduit;
    }


}
