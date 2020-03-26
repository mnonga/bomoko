<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pub
 *
 * @ORM\Table(name="pub", indexes={@ORM\Index(name="dateheure", columns={"dateheure"}), @ORM\Index(name="user", columns={"user"}), @ORM\Index(name="categorie", columns={"categorie"})})
 * @ORM\Entity(repositoryClass="UserBundle\Entity\PubRepository")
 */
class Pub
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @Assert\Length(min=5, minMessage="Le titre doit avoir au moins {{ limit }} caractères."))
     * @Assert\NotBlank(message="Le titre ne doit pas etre vide.")
     * @ORM\Column(name="titre", type="string", length=255, nullable=false)
     */
    private $titre;

    /**
     * @var string
     * @Assert\NotBlank(message="Le contenu ne doit pas etre vide.")
     * @Assert\Length(min=30, minMessage="Le contenu doit avoir au moins {{ limit }} caractères."))
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=false)
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateheure", type="datetime", nullable=false)
     */
    private $dateheure;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_comment", type="integer", nullable=false)
     */
    private $nbComment;


    /**
     * @var integer
     *
     * @ORM\Column(name="nb_like", type="integer", nullable=false)
     */
    private $nbLike;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_dislike", type="integer", nullable=false)
     */
    private $nbDislike;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="userid")
     * })
     */
    private $user;

    /**
     * @var \Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie", referencedColumnName="id")
     * })
     */
    private $categorie;

    /**
    * @ORM\OneToMany(
    * targetEntity="\UserBundle\Entity\File",
    * mappedBy="pub",
    * orphanRemoval=true, cascade={"persist","remove"}
    * )
    * @Assert\Count(min=0, max=10, maxMessage="Pas plus de {{ limit }} images par Publication.")
    */
    private $files;




    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->uploadedFiles=array();
        $this->dateheure=new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Pub
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Pub
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set dateheure
     *
     * @param \DateTime $dateheure
     *
     * @return Pub
     */
    public function setDateheure($dateheure)
    {
        $this->dateheure = $dateheure;

        return $this;
    }

    /**
     * Get dateheure
     *
     * @return \DateTime
     */
    public function getDateheure()
    {
        return $this->dateheure;
    }


    /**
     * Set nbComment
     *
     * @param integer $nbLike
     *
     * @return Pub
     */
    public function setNbComment($nbComment)
    {
        $this->nbComment = $nbComment;

        return $this;
    }

    /**
     * Get nbComment
     *
     * @return integer
     */
    public function getNbComment()
    {
        return $this->nbComment;
    }


    /**
     * Set nbLike
     *
     * @param integer $nbLike
     *
     * @return Pub
     */
    public function setNbLike($nbLike)
    {
        $this->nbLike = $nbLike;

        return $this;
    }

    /**
     * Get nbLike
     *
     * @return integer
     */
    public function getNbLike()
    {
        return $this->nbLike;
    }

    /**
     * Set nbDislike
     *
     * @param integer $nbDislike
     *
     * @return Pub
     */
    public function setNbDislike($nbDislike)
    {
        $this->nbDislike = $nbDislike;

        return $this;
    }

    /**
     * Get nbDislike
     *
     * @return integer
     */
    public function getNbDislike()
    {
        return $this->nbDislike;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Pub
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set categorie
     *
     * @param \UserBundle\Entity\Categorie $categorie
     *
     * @return Pub
     */
    public function setCategorie(\UserBundle\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \UserBundle\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Add file
     *
     * @param \UserBundle\Entity\File $file
     *
     * @return Pub
     */
    public function addFile(\UserBundle\Entity\File $file)
    {
        $this->files[] = $file;
        $file->setPub($this);
        return $this;
    }

    /**
     * Remove file
     *
     * @param \UserBundle\Entity\File $file
     */
    public function removeFile(\UserBundle\Entity\File $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }
    /**
     * Set files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setFiles(\Doctrine\Common\Collections\ArrayCollection $files)
    {
        $this->files=$files;
        return $this;
    }

    /**
    * 
    * @Assert\Count(min=0, max=10, maxMessage="Pas plus de {{ limit }} images par Publication.")
    */
    private $uploadedFiles;
    public function setUploadedFiles($files)
    {
        $this->uploadedFiles=$files;
    }
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }


    //un holder pour marquer une pub comme liker, disliker ou rien
    private $likeExtra;

    public function setLikeExtra(string $value){
        $this->likeExtra=$value;
    }
    public function getLikeExtra(){
        return $this->likeExtra;
    }

}
