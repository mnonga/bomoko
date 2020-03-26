<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment", indexes={@ORM\Index(name="ref_comment", columns={"ref_comment"}), @ORM\Index(name="user", columns={"user"}), @ORM\Index(name="pub", columns={"pub"}), @ORM\Index(name="dateheure", columns={"dateheure"})})
 * @ORM\Entity
 */
class Comment
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateheure", type="datetime", nullable=false)
     */
    private $dateheure = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=255, nullable=false)
     */
    private $contenu;

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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="userid")
     * })
     */
    private $user;

    /**
     * @var \Comment
     *
     * @ORM\ManyToOne(targetEntity="Comment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ref_comment", referencedColumnName="id")
     * })
     */
    private $refComment;



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
     * Set dateheure
     *
     * @param \DateTime $dateheure
     *
     * @return Comment
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
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Comment
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
     * Set pub
     *
     * @param \UserBundle\Entity\Pub $pub
     *
     * @return Comment
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

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Comment
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
     * Set refComment
     *
     * @param \UserBundle\Entity\Comment $refComment
     *
     * @return Comment
     */
    public function setRefComment(\UserBundle\Entity\Comment $refComment = null)
    {
        $this->refComment = $refComment;

        return $this;
    }

    /**
     * Get refComment
     *
     * @return \UserBundle\Entity\Comment
     */
    public function getRefComment()
    {
        return $this->refComment;
    }
}
