<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 */
class Categorie implements \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_categorie", type="string", length=255, nullable=false)
     */
    private $nomCategorie;

    /**
     * Categorie constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNomCategorie()
    {
        return $this->nomCategorie;
    }

    /**
     * @param string $nomCategorie
     */
    public function setNomCategorie(string $nomCategorie)
    {
        $this->nomCategorie = $nomCategorie;
    }

    public function serialize(){
        return serialize(array(
            $this->id,
            $this->nomCategorie
        ));
    }

    public function unserialize($serialized){
        list(

            $this->id,
            $this->nomCategorie
            ) = unserialize($serialized);
    }


}
