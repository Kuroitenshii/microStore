<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Produits
 *
 * @ORM\Table(name="produits", indexes={@ORM\Index(name="id_categorie", columns={"id_categorie"})})
 * @ORM\Entity
 */
class Produits
{
    /**
     * @var int
     *
     * @ORM\Column(name="ref", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="text", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee_produit", type="integer", nullable=false)
     */
    private $annee;

    /**
     * @var string
     *
     * @ORM\Column(name="fabricant", type="text", length=255, nullable=false)
     */
    private $fabricant;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=false)
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", length=65535, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_fabricant", type="blob", length=65535, nullable=false)
     */
    private $imageFabricant;

    /**
     * @var Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categorie", referencedColumnName="id", onDelete="set null")
     * })
     */
    private $idCategorie;

    /**
     * Produits constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param int $ref
     */
    public function setRef(int $ref)
    {
        $this->ref = $ref;
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * @param int $annee
     */
    public function setAnnee(int $annee)
    {
        $this->annee = $annee;
    }

    /**
     * @return string
     */
    public function getFabricant()
    {
        return $this->fabricant;
    }

    /**
     * @param string $fabricant
     */
    public function setFabricant(string $fabricant)
    {
        $this->fabricant = $fabricant;
    }

    /**
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * @param float $prix
     */
    public function setPrix(float $prix)
    {
        $this->prix = $prix;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image)
    {
        $this->image = $image;
    }

    /**
     * @return Categorie
     */
    public function getIdCategorie()
    {
        return $this->idCategorie;
    }

    /**
     * @param Categorie $idCategorie
     */
    public function setIdCategorie(Categorie $idCategorie)
    {
        $this->idCategorie = $idCategorie;
    }

    /**
     * @return blob
     */
    public function getImageFabricant()
    {
        return $this->imageFabricant;
    }

    /**
     * @param string $imageFabricant
     */
    public function setImageFabricant(string $imageFabricant)
    {
        $this->imageFabricant = $imageFabricant;
    }


}
