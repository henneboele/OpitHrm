<?php

/*
 *  This file is part of the OPIT-HRM project.
 *
 *  (c) Opit Consulting Kft. <info@opit.hu>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\OpitHrm\LeaveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opit\OpitHrm\StatusBundle\Entity\Status;
use Opit\OpitHrm\LeaveBundle\Entity\LeaveRequest;
use Opit\OpitHrm\CoreBundle\Entity\AbstractBase;

/**
 * This class is a container for the Travel Expense Status model
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package OPIT-HRM
 * @subpackage LeaveBundle
 *
 * @ORM\Table(name="opithrm_states_leave_request")
 * @ORM\Entity(repositoryClass="Opit\OpitHrm\LeaveBundle\Entity\StatesLeaveRequestsRepository")
 */
class StatesLeaveRequests extends AbstractBase
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LeaveRequest", inversedBy="states", fetch="EAGER")
     * @ORM\JoinColumn(name="leave_request_id", referencedColumnName="id")
     */
    protected $leaveRequest;

     /**
     * @ORM\ManyToOne(targetEntity="\Opit\OpitHrm\StatusBundle\Entity\Status", fetch="EAGER")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status;

    /**
     * @ORM\OneToOne(targetEntity="CommentLeaveStatus", mappedBy="status", cascade={"persist", "remove"})
     */
    protected $comment;

    public function __construct(Status $status = null, LeaveRequest $leaveRequest = null)
    {
        parent::__construct();
        $this->setStatus($status);
        $this->setLeaveRequest($leaveRequest);
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
     * Set leaveRequest
     *
     * @param \Opit\OpitHrm\LeaveBundle\Entity\LeaveRequest $leaveRequest
     * @return StatesLeaveRequest
     */
    public function setLeaveRequest(\Opit\OpitHrm\LeaveBundle\Entity\LeaveRequest $leaveRequest = null)
    {
        $this->leaveRequest = $leaveRequest;

        return $this;
    }

    /**
     * Get leaveRequest
     *
     * @return \Opit\OpitHrm\LeaveBundle\Entity\LeaveRequest
     */
    public function getLeaveRequest()
    {
        return $this->leaveRequest;
    }

    /**
     * Set status
     *
     * @param \Opit\OpitHrm\StatusBundle\Entity\Status $status
     * @return StatesLeaveRequest
     */
    public function setStatus(\Opit\OpitHrm\StatusBundle\Entity\Status $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Opit\OpitHrm\StatusBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set comment
     *
     * @param \Opit\OpitHrm\LeaveBundle\Entity\CommentLeaveStatus $comment
     * @return StatesLeaveRequests
     */
    public function setComment(\Opit\OpitHrm\LeaveBundle\Entity\CommentLeaveStatus $comment = null)
    {
        $comment->setStatus($this);
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \Opit\OpitHrm\LeaveBundle\Entity\CommentLeaveStatus
     */
    public function getComment()
    {
        return $this->comment;
    }
}
