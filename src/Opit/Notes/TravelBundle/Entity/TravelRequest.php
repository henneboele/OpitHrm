<?php

namespace Opit\Notes\TravelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * travel_request
 *
 * @ORM\Table(name="notes_travel_request")
 * @ORM\Entity(repositoryClass="Opit\Notes\TravelBundle\Entity\TravelRequestRepository")
 */
class TravelRequest
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
      * @ORM\ManyToOne(targetEntity="TravelRequestUser", inversedBy="travelRequests")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="departure_date", type="date")
     */
    private $departureDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrival_date", type="date")
     */
    private $arrivalDate;

    /**
     * @var string
     *
     * @ORM\Column(name="trip_purpose", type="string", length=255)
     */
    private $tripPurpose;

    /**
     * @ORM\ManyToOne(targetEntity="TravelRequestUser", inversedBy="travelRequestsTM")
     */
    private $teamManager;

    /**
      * @ORM\ManyToOne(targetEntity="TravelRequestUser", inversedBy="travelRequestsGM")
     */
    private $generalManager;

    /**
     * @var boolean
     *
     * @ORM\Column(name="customer_related", type="boolean")
     */
    private $customerRelated;

    /**
     * @var string
     *
     * @ORM\Column(name="opportunity_name", type="string", length=255)
     */
    private $opportunityName;
    
    /**
     * @ORM\OneToMany(targetEntity="TRDestination", mappedBy="travelRequest", cascade={"persist", "remove"})
     */
    private $destinations;
    
    /**
     * @ORM\OneToMany(targetEntity="TRAccomodation", mappedBy="travelRequest", cascade={"persist", "remove"})
     */
    private $accomodations;

    
    
    public function __construct()
    {
        $this->destinations = new ArrayCollection();
        $this->accomodations = new ArrayCollection();
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
     * Set departureDate
     *
     * @param \DateTime $departureDate
     * @return travel_request
     */
    public function setDepartureDate($departureDate)
    {
        $this->departureDate = $departureDate;
    
        return $this;
    }

    /**
     * Get departureDate
     *
     * @return \DateTime
     */
    public function getDepartureDate()
    {
        return $this->departureDate;
    }

    /**
     * Set arrivalDate
     *
     * @param \DateTime $arrivalDate
     * @return travel_request
     */
    public function setArrivalDate($arrivalDate)
    {
        $this->arrivalDate = $arrivalDate;
    
        return $this;
    }

    /**
     * Get arrivalDate
     *
     * @return \DateTime
     */
    public function getArrivalDate()
    {
        return $this->arrivalDate;
    }

    /**
     * Set tripPurpose
     *
     * @param string $tripPurpose
     * @return travel_request
     */
    public function setTripPurpose($tripPurpose)
    {
        $this->tripPurpose = $tripPurpose;
    
        return $this;
    }

    /**
     * Get tripPurpose
     *
     * @return string
     */
    public function getTripPurpose()
    {
        return $this->tripPurpose;
    }

    /**
     * Set customerRelated
     *
     * @param boolean $customerRelated
     * @return travel_request
     */
    public function setCustomerRelated($customerRelated)
    {
        $this->customerRelated = $customerRelated;
    
        return $this;
    }

    /**
     * Get customerRelated
     *
     * @return boolean
     */
    public function getCustomerRelated()
    {
        return $this->customerRelated;
    }

    /**
     * Set opportunityName
     *
     * @param string $opportunityName
     * @return travel_request
     */
    public function setOpportunityName($opportunityName)
    {
        $this->opportunityName = $opportunityName;
    
        return $this;
    }

    /**
     * Get opportunityName
     *
     * @return string
     */
    public function getOpportunityName()
    {
        return $this->opportunityName;
    }

    /**
     * Add destinations
     *
     * @param \Opit\Notes\TravelBundle\Entity\TRDestination $destinations
     * @return TravelRequest
     */
    public function addDestination(\Opit\Notes\TravelBundle\Entity\TRDestination $destinations)
    {
        $this->destinations[] = $destinations;
        $destinations->setTravelRequest($this); // synchronously updating inverse side
    
        return $this;
    }

    /**
     * Remove destinations
     *
     * @param \Opit\Notes\TravelBundle\Entity\TRDestination $destinations
     */
    public function removeDestination(\Opit\Notes\TravelBundle\Entity\TRDestination $destinations)
    {
        $this->destinations->removeElement($destinations);
    }

    /**
     * Get destinations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDestinations()
    {
        return $this->destinations;
    }

    /**
     * Add accomodations
     *
     * @param \Opit\Notes\TravelBundle\Entity\TRAccomodation $accomodations
     * @return TravelRequest
     */
    public function addAccomodation(\Opit\Notes\TravelBundle\Entity\TRAccomodation $accomodations)
    {
        $this->accomodations[] = $accomodations;
        $accomodations->setTravelRequest($this); // synchronously updating inverse side
    
        return $this;
    }

    /**
     * Remove accomodations
     *
     * @param \Opit\Notes\TravelBundle\Entity\TRAccomodation $accomodations
     */
    public function removeAccomodation(\Opit\Notes\TravelBundle\Entity\TRAccomodation $accomodations)
    {
        $this->accomodations->removeElement($accomodations);
    }

    /**
     * Get accomodations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccomodations()
    {
        return $this->accomodations;
    }

    /**
     * Set user
     *
     * @param \Opit\Notes\TravelBundle\Entity\User $user
     * @return TravelRequest
     */
    public function setUser(\Opit\Notes\TravelBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Opit\Notes\TravelBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set teamManager
     *
     * @param \Opit\Notes\TravelBundle\Entity\User $teamManager
     * @return TravelRequest
     */
    public function setTeamManager(\Opit\Notes\TravelBundle\Entity\User $teamManager = null)
    {
        $this->teamManager = $teamManager;
    
        return $this;
    }

    /**
     * Get teamManager
     *
     * @return \Opit\Notes\TravelBundle\Entity\User
     */
    public function getTeamManager()
    {
        return $this->teamManager;
    }

    /**
     * Set generalManager
     *
     * @param \Opit\Notes\TravelBundle\Entity\User $generalManager
     * @return TravelRequest
     */
    public function setGeneralManager(\Opit\Notes\TravelBundle\Entity\User $generalManager = null)
    {
        $this->generalManager = $generalManager;
    
        return $this;
    }

    /**
     * Get generalManager
     *
     * @return \Opit\Notes\TravelBundle\Entity\User
     */
    public function getGeneralManager()
    {
        return $this->generalManager;
    }
}