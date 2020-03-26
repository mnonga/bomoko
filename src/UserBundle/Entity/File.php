<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="file", indexes={@ORM\Index(name="pub", columns={"pub"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class File
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
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

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
     * Set path
     *
     * @param string $path
     *
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set pub
     *
     * @param \UserBundle\Entity\Pub $pub
     *
     * @return File
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
    * @ORM\PostRemove
    */
    public function deleteRelatedFile()
    {
        global $kernel;
        $kernel->getContainer()->getParameter('pub_images_dir');
        $file=__DIR__.'/../../../web/'.'files/pub/images/'.$this->path;
        try{
            unlink($file);    
        }catch(Exception $e){
            
        }
        
    }
}
