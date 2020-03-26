<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PubLike
 *
 * @ORM\Table(name="pub_like", uniqueConstraints={@ORM\UniqueConstraint(name="unique_pub_liker", columns={"pub", "liker"})}, indexes={@ORM\Index(name="liker_user", columns={"liker"}), @ORM\Index(name="IDX_57AD8915A443C85", columns={"pub"})})
 * @ORM\Entity
 */
class PubLike
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
     * @var boolean
     *
     * @ORM\Column(name="is_like", type="boolean", nullable=false)
     */
    private $isLike = '1';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="liker", referencedColumnName="userid")
     * })
     */
    private $liker;

    /**
     * @var \Pub
     *
     * @ORM\ManyToOne(targetEntity="Pub")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pub", referencedColumnName="id")
     * })
     */
    private $pub;

    


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
     * Set isLike
     *
     * @param boolean $isLike
     *
     * @return PubLike
     */
    public function setIsLike($isLike)
    {
        $this->isLike = $isLike;

        return $this;
    }

    /**
     * Get isLike
     *
     * @return boolean
     */
    public function getIsLike()
    {
        return $this->isLike;
    }

    /**
     * Set liker
     *
     * @param \UserBundle\Entity\User $liker
     *
     * @return PubLike
     */
    public function setLiker(\UserBundle\Entity\User $liker = null)
    {
        $this->liker = $liker;

        return $this;
    }

    /**
     * Get liker
     *
     * @return \UserBundle\Entity\User
     */
    public function getLiker()
    {
        return $this->liker;
    }

    /**
     * Set pub
     *
     * @param \UserBundle\Entity\Pub $pub
     *
     * @return PubLike
     */
    public function setPub(\UserBundle\Entity\Pub $pub = null)
    {
        $this->pub = $pub;

        return $this;
    }

    /**
     * Get pub
     *
     * @return \UserBundle\Entity\Pub
     */
    public function getPub()
    {
        return $this->pub;
    }
}
