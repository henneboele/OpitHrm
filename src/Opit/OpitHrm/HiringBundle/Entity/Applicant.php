<?php

/*
 *  This file is part of the OPIT-HRM project.
 *
 *  (c) Opit Consulting Kft. <info@opit.hu>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\OpitHrm\HiringBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Opit\OpitHrm\CoreBundle\Entity\AbstractBase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Applicant
 *
 * @ORM\Table(name="opithrm_applicants")
 * @ORM\Entity(repositoryClass="Opit\OpitHrm\HiringBundle\Entity\ApplicantRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Applicant extends AbstractBase
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
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     * @Assert\NotBlank(message="Applicant name can not be empty.")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     * @Assert\NotBlank(message="Applicant email can not be empty")
     * @Assert\Email(message = "The email address is not valid.")
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string")
     * @Assert\NotBlank(message="Applicant phone number can not be empty")
     */
    protected $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string")
     * @Assert\NotBlank(message="Keywords can not be empty")
     */
    protected $keywords;

    /**
     * @Assert\File(
     *  maxSize="5M",
     *  mimeTypes = {"application/pdf", "application/x-pdf", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/msword"},
     *  mimeTypesMessage = "Invalid CV file format. Supported formats are .pdf, .doc, .docx"
     * )
     * @ORM\Column(name="cvFile", type="string", nullable=true)
     */
    private $cvFile;

    /**
     * @var string
     *
     * @ORM\Column(name="cv", type="string", nullable=true)
     */
    protected $cv;

    /**
     * @var date
     *
     * @ORM\Column(name="applicationDate", type="date")
     * @Assert\NotBlank(message="Application date can not be empty")
     */
    protected $applicationDate;

    /**
     * @ORM\ManyToOne(targetEntity="JobPosition", inversedBy="applicants")
     * @ORM\JoinColumn(name="job_position_id", referencedColumnName="id")
     **/
    private $jobPosition;

    /**
     * @ORM\OneToMany(targetEntity="StatesApplicants", mappedBy="applicant", cascade={"persist", "remove"})
     */
    protected $states;

    /**
     * @ORM\OneToMany(targetEntity="ApplicantNotification", mappedBy="applicant", cascade={"remove"})
     */
    protected $notifications;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->states = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Applicant
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
     * Set email
     *
     * @param string $email
     * @return Applicant
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
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Applicant
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Applicant
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set cv
     *
     * @param string $cv
     * @return Applicant
     */
    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * Get cv
     *
     * @return string
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * Set cvFile
     *
     * @param string $cvFile
     * @return Applicant
     */
    public function setCvFile(UploadedFile $cvFile)
    {
        $this->cvFile = $cvFile;

        return $this;
    }

    /**
     * Get cvFile
     *
     * @return string
     */
    public function getCvFile()
    {
        return $this->cvFile;
    }

    /**
     * Set applicationDate
     *
     * @param \DateTime $applicationDate
     * @return Applicant
     */
    public function setApplicationDate($applicationDate)
    {
        $this->applicationDate = $applicationDate;

        return $this;
    }

    /**
     * Get applicationDate
     *
     * @return \DateTime
     */
    public function getApplicationDate()
    {
        return $this->applicationDate;
    }

    /**
     * Set jobPosition
     *
     * @param \Opit\OpitHrm\HiringBundle\Entity\JobPosition $jobPosition
     * @return Applicant
     */
    public function setJobPosition(\Opit\OpitHrm\HiringBundle\Entity\JobPosition $jobPosition = null)
    {
        $this->jobPosition = $jobPosition;

        return $this;
    }

    /**
     * Get jobPosition
     *
     * @return \Opit\OpitHrm\HiringBundle\Entity\JobPosition
     */
    public function getJobPosition()
    {
        return $this->jobPosition;
    }

    /**
     * Add states
     *
     * @param \Opit\OpitHrm\HiringBundle\Entity\StatesApplicants $states
     * @return Applicant
     */
    public function addState(\Opit\OpitHrm\HiringBundle\Entity\StatesApplicants $states)
    {
        $this->states[] = $states;

        return $this;
    }

    /**
     * Remove states
     *
     * @param \Opit\OpitHrm\HiringBundle\Entity\StatesApplicants $states
     */
    public function removeState(\Opit\OpitHrm\HiringBundle\Entity\StatesApplicants $states)
    {
        $this->states->removeElement($states);
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Returns the absolute path to a file
     *
     * @return type
     */
    public function getAbsolutePath()
    {
        return null === $this->cv ? null : $this->getUploadRootDir().'/'.$this->cv;
    }

    /**
     * The absolute directory path where the CVs should be saved
     *
     * @return type
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    /**
     * Return web path which can be used in a template to link to the file
     *
     * @return type
     */
    public function getWebPath()
    {
        return null === $this->cv ? null : $this->getUploadDir().'/'.$this->cv;
    }

    /**
     * Gets the files upload directory
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'docs/cv';
    }

    /**
     * Upload CV for applicant and remove old CV if there was one
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function uploadCV()
    {
        if (null === $this->getCvFile()) {
            return;
        }

        if (null !== $this->getId()){
            unlink($this->getUploadRootDir(). '/' . $this->getCV());
        }

        $now = new \DateTime();
        $originalCVFileName = explode('.', $this->getCvFile()->getClientOriginalName());
        $originalCVFileName[count($originalCVFileName) - 2] = $originalCVFileName[count($originalCVFileName) - 2] . '_' . $now->getTimestamp();
        $originalCVFileName = implode('.', $originalCVFileName);

        $this->getCvFile()->move(
            $this->getUploadRootDir(),
            $originalCVFileName
        );

        $this->cv = $originalCVFileName;
        $this->cvFile = null;
    }

    /**
     * Validate CV if new applicant is being added
     * 
     * @Assert\Callback
     */
    public function validateLeaveDates(ExecutionContextInterface $context)
    {
        if (null === $this->getId() && null === $this->getCvFile()) {
            $context->addViolation('Applicant CV can not be empty');
        }
    }
}
