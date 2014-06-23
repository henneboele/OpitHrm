<?php

/*
 *  This file is part of the {Bundle}.
 *
 *  (c) Opit Consulting Kft. <info@opit.hu>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\Notes\LeaveBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Opit\Notes\LeaveBundle\Entity\LeaveRequest;

/**
 * Description of LeaveRequestPostListener
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package Opit
 * @subpackage Notes
 */
class LeaveRequestPostListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof LeaveRequest) {
            $leaveRequestId = str_replace(
                array('{year}', '{id}'),
                array(date('y'), sprintf('%05d', $entity->getId())),
                LeaveRequest::getIDPattern()
            );

            $entity->setLeaveRequestId($leaveRequestId);
            $entityManager->persist($entity);
            $entityManager->flush();
        }
    }
}
