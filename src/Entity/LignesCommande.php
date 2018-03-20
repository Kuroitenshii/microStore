<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * LignesCommande
 *
 * @ORM\Table(name="lignes_commande")
 * @ORM\Entity
 */
class LignesCommande
{
    /**
     * @var \Commande
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Commande")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_commande", referencedColumnName="id_commande", onDelete="cascade")
     * })
     */
    private $idCommande;

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
     * @var float
     *
     * @ORM\Column(name="quantite_commande", type="float", precision=10, scale=0, nullable=false)
     */
    private $quantiteCommande;

    /**
     * LignesCommande constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return \Commande
     */
    public function getIdCommande()
    {
        return $this->idCommande;
    }

    /**
     * @param Commande $idCommande
     */
    public function setIdCommande(Commande $idCommande)
    {
        $this->idCommande = $idCommande;
    }

    /**
     * @return \Produits
     */
    public function getRefProduit()
    {
        return $this->refProduit;
    }

    /**
     * @param Produits $refProduit
     */
    public function setRefProduit(Produits $refProduit)
    {
        $this->refProduit = $refProduit;
    }

    /**
     * @return float
     */
    public function getQuantiteCommande()
    {
        return $this->quantiteCommande;
    }

    /**
     * @param float $quantiteCommande
     */
    public function setQuantiteCommande(float $quantiteCommande)
    {
        $this->quantiteCommande = $quantiteCommande;
    }


}
