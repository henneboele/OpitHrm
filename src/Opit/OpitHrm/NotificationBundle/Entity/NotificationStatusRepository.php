<?php

/*
 *  This file is part of the OPIT-HRM project.
 * 
 *  (c) Opit Consulting Kft. <info@opit.hu>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\OpitHrm\NotificationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of NotificationStatusRepository
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package OPIT-HRM
 * @subpackage NotificationBundle
 */
class NotificationStatusRepository extends EntityRepository
{
    public function getLastStatus()
    {
        $lastNotificationStatus = $this->createQueryBuilder('ns')
            ->add('orderBy', 'ns.id DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $lastNotificationStatus->getOneOrNullResult();
    }
}
