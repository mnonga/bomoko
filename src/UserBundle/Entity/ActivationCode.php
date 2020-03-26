<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivationCode
 *
 * @ORM\Table(name="activation_code", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class ActivationCode
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=32, nullable=false)
     */
    private $code;

    /**
     * @var \User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     * })
     */
    private $userid;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateheure", type="datetime", nullable=true)
     */
    private $dateheure;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateheure=new \DateTime();
    }


    /**
     * Set code
     *
     * @param string $code
     *
     * @return ActivationCode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set dateheure
     *
     * @param \DateTime $dateheure
     *
     * @return ActivationCode
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
     * Set userid
     *
     * @param \UserBundle\Entity\User $userid
     *
     * @return ActivationCode
     */
    public function setUserid(\UserBundle\Entity\User $userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \UserBundle\Entity\User
     */
    public function getUserid()
    {
        return $this->userid;
    }
}
