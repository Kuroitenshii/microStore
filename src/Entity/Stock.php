<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity
 */
class Stock
{
    /**
     * @var int
     *
     * @ORM\Column(name="quantite_stock", type="integer", nullable=false)
     */
    private $quantiteStock;

    /**
     * @var \Produits
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Produits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_produit", referencedColumnName="ref", onDelete="cascade")
     * })
     */
    private $refProduit;

    /**
     * Stock constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getQuantiteStock()
    {
        return $this->quantiteStock;
    }

    /**
     * @param int $quantiteStock
     */
    public function setQuantiteStock(int $quantiteStock)
    {
        $this->quantiteStock = $quantiteStock;
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


}
