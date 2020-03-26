<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

define('STATE_NOT_ACTIVATED', 'NOT_ACTIVATED');
define('STATE_NORMAL', 'NORMAL');
define('STATE_BLOCKED', 'BLOCKED');
define('STATE_RESET_PASSWORD', 'RESET_PASSWORD');

/**
 * User
 * @UniqueEntity(fields="email", message="Cet email est déjà pris !")
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @var integer
     * 
     * @ORM\Column(name="userid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userid;

    /**
     * @var string
     * @Assert\Length(min=5, max=100, minMessage="Le nom doit faire au moins {{ limit }} caractères.", maxMessage="Le nom doit faire au plus {{ max }} caractères."))
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var \Date
     * @Assert\Date(message="Veuillez renseigner une date valide.")
     * @Assert\GreaterThan("-120 years")
     * @Assert\LessThan("today", message="La date de naissance doit etre superieur à aujourd'hui!")
     * @ORM\Column(name="datenaissance", type="date", nullable=true)
     */
    private $datenaissance;

    /**
     * @var string
     * @Assert\NotBlank(message="L'adresse ne doit pas etre blanc.")
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *  @Assert\Regex(pattern="/^M|F$/", message="Votre sexe est non valide!")
     * @ORM\Column(name="sexe", type="string", nullable=false)
     */
    private $sexe;

    /**
     * @var string
     * @Assert\Length(min=5, minMessage="Le mot de passe doit faire au moins {{ limit }} caractères."))
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;


/*/**
     * @var string
     * @Assert\NotBlank(message="Veuillez choisir une photo de profile.")
     * @Assert\Image(maxSize="1024Ki", maxSizeMessage="L'image ne doit pas peser plus de 1024Ko.", 
     * uploadErrorMessage="Erreur de l'envoi de l'image.",
     * corruptedMessage="Image corrompu !")
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */

    /**
     * @var string
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var string
     *  @Assert\Email(message="Veuillez renseigner un email valid !")
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", nullable=false)
     */
    private $etat = 'NOT_ACTIVATED';

    //Added for security
    
    /**
    * @var string
    *
    * @ORM\Column(name="salt", type="string", length=255)
    */
    private $salt;
    /**
    * @var array
    *
    * @ORM\Column(name="roles", type="array")
    */
    private $roles;

    public function eraseCredentials(){}

    public function __construct()
    {
        $this->roles = array();
    }

    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set datenaissance
     *
     * @param \Date $datenaissance
     *
     * @return User
     */
    public function setDatenaissance($datenaissance)
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    /**
     * Get datenaissance
     *
     * @return \Date
     */
    public function getDatenaissance()
    {
        return $this->datenaissance;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return User
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return User
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return User
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function serialize()
    {
        return serialize(array(
            $this->userid, $this->telephone, $this->password, $this->salt
            //$this->isActive
        ));
    }
    public function unserialize($serialized)
    {
        list(
            $this->userid, $this->telephone, $this->password, $this->salt
            //$this->isActive
        )=unserialize($serialized);
    }
    /**
     * Get username(the login here is the phone number, so it returns telephone)
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->name;
    }

    /**
    * @ORM\PostRemove
    */
    public function deleteRelatedFile()
    {
        $file=__DIR__.'/../../../web/'.'files/user/profiles'.$this->photo;
        unlink($file);
    }
}
