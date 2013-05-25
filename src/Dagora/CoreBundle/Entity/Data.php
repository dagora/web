<?php

namespace Dagora\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    TE\DoctrineBehaviorsBundle as Behaviors;

/**
 * Dagora\CoreBundle\Entity\Data
 *
 * @ORM\Table("data")
 * @ORM\Entity(repositoryClass="Dagora\CoreBundle\Entity\DataRepository")
 */
class Data
{
    use Behaviors\Model\JSONBindable,
        Behaviors\Model\Timestampable;

    /**
     * Allowed parameters to bind
     *
     * @var array
     */
    protected static $allowedParams = array('source', 'date', 'value');

    /**
     * @var Source $source
     *
     * @ORM\ManyToOne(targetEntity="Source")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id", nullable=false)
     */
    private $source;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var date $date
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var int $value
     *
     * @ORM\Column(name="value", type="string")
     */
    private $value;

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
     * Set source
     *
     * @param source $source
     * @return sourcePlace
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set value
     *
     * @param  string $value
     * @return Area
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}