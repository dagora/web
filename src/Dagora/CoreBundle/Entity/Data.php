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
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string $dateType
     *
     * @ORM\Column(name="date_type", type="string", length=1)
     */
    private $dateType;

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
     * @return Data
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
     * Set date
     *
     * @param date $date
     * @return Data
     */
    public function setDate($date)
    {
        $dateAr = explode('-', $date);

        // year
        if ( 1 == count($dateAr) ) {
            $this->date     = new \DateTime($date.'-01-01');
            $this->dateType = 'y';
        }
        // month
        if ( 2 == count($dateAr) ) {
            $this->date     = new \DateTime($date.'-01');
            $this->dateType = 'm';
        }
        // day
        if ( 3 == count($dateAr) ) {
            $this->date = new \DateTime($date);
            $this->dateType = 'd';
        }
        return $this;
    }

    /**
     * Get date
     *
     * @return date
     */
    public function getDate()
    {
        switch ($this->dateType) {
            case 'y':
                return $this->date->format('Y');
                break;

            case 'm':
                return $this->date->format('Y-m');
                break;

            case 'd':
                return $this->date->format('Y-m-d');
                break;

            default:
                return '';
        }
    }

    /**
     * Set dateType
     *
     * @param  string $dateType
     * @return Data
     */
    public function setDateType($dateType)
    {
        $this->dateType = $dateType;
        return $this;
    }

    /**
     * Get dateType
     *
     * @return string
     */
    public function getDateType()
    {
        return $this->dateType;
    }

    /**
     * Set value
     *
     * @param  double $value
     * @return Data
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return double
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return array for API
     *
     * @param array $extraData
     * @return array
     */
    public function asApiArray($extraData=array()) {

        // Basic fields
        $array = array(
            'value' => (double) $this->value,
            'date'  => $this->getDate()
        );

        return $array;
    }

}