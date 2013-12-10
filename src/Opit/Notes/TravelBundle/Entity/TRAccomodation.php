<?php

namespace Opit\Notes\TravelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TRAccomodation
 *
 * @ORM\Table(name="notes_tr_accomodation")
 * @ORM\Entity(repositoryClass="Opit\Notes\TravelBundle\Entity\TRAccomodationRepository")
 */
class TRAccomodation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_of_nights", type="integer")
     */
    private $numberOfNights;

    /**
     * @var integer
     *
     * @ORM\Column(name="cost", type="integer")
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="hotel_name", type="string", length=255)
     */
    private $hotelName;

    /**
     * @ORM\ManyToOne(targetEntity="TravelRequest", inversedBy="accomodations")
     */
    protected $travelRequest;
    
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
     * Set numberOfNights
     *
     * @param integer $numberOfNights
     * @return TRAccomodation
     */
    public function setNumberOfNights($numberOfNights)
    {
        $this->numberOfNights = $numberOfNights;
    
        return $this;
    }

    /**
     * Get numberOfNights
     *
     * @return integer
     */
    public function getNumberOfNights()
    {
        return $this->numberOfNights;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     * @return TRAccomodation
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    
        return $this;
    }

    /**
     * Get cost
     *
     * @return integer
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return TRAccomodation
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set hotelName
     *
     * @param string $hotelName
     * @return TRAccomodation
     */
    public function setHotelName($hotelName)
    {
        $this->hotelName = $hotelName;
    
        return $this;
    }

    /**
     * Get hotelName
     *
     * @return string
     */
    public function getHotelName()
    {
        return $this->hotelName;
    }

    /**
     * Set travelRequest
     *
     * @param \Opit\Notes\TravelBundle\Entity\TravelRequest $travelRequest
     * @return TRAccomodation
     */
    public function setTravelRequest(\Opit\Notes\TravelBundle\Entity\TravelRequest $travelRequest = null)
    {
        $this->travelRequest = $travelRequest;
    
        return $this;
    }

    /**
     * Get travelRequest
     *
     * @return \Opit\Notes\TravelBundle\Entity\TravelRequest
     */
    public function getTravelRequest()
    {
        return $this->travelRequest;
    }
}