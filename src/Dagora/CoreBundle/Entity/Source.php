<?php

namespace Dagora\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    TE\DoctrineBehaviorsBundle as Behaviors;

/**
 * Dagora\CoreBundle\Entity\Source
 *
 * @ORM\Table("source")
 * @ORM\Entity(repositoryClass="Dagora\CoreBundle\Entity\SourceRepository")
 */
class Source
{
    use Behaviors\Model\JSONBindable,
        Behaviors\Model\Timestampable;

    /**
     * Allowed parameters to bind
     *
     * @var array
     */
    protected static $allowedParams = array('title', 'link', 'unit');

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string $link
     *
     * @ORM\Column(name="link", type="string")
     */
    private $link;

    /**
     * @var string $unit
     *
     * @ORM\Column(name="unit", type="string")
     */
    private $unit;

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
     * Set title
     *
     * @param  string $title
     * @return Area
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set link
     *
     * @param  string $link
     * @return Area
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set unit
     *
     * @param  string $unit
     * @return Area
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
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
            'id'    => (int) $this->id,
            'title' => $this->title,
            'link'  => $this->link,
            'unit'  => $this->unit
        );

        if ( isset($extraData['data']) ) {

            $array['data'] = array();

            foreach ($extraData['data'] as $data) {
                $array['data'][] = $data->asApiArray();
            }
        }

        return $array;
    }

}