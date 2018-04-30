<?php
namespace App\Entity;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Panier
 *
 * @ORM\Table(name="panier", indexes={@ORM\Index(name="refPanier", columns={"ref_produit"})})
 * @ORM\Entity
 */
class Panier
{
    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_client", referencedColumnName="id")
     * })
     */
    private $idClient;

    /**
     * @var Produits
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Produits", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_produit", referencedColumnName="ref")
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
     * @return string
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
     * @return Produits
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

    public function serialize(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $total = $this->quantiteProduit*$this->refProduit->getPrix();
        $serializer = new Serializer($normalizers, $encoders);
        return $serializer->serialize(array(
            'Référence' => $this->refProduit->getNom(),
            'Quantite' => $this->quantiteProduit,
            'prix' => $this->refProduit->getPrix(),
            'total' => $total,
        ), 'json');
    }


}
