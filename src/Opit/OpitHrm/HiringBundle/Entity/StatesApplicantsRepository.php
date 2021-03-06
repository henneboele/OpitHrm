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

use Doctrine\ORM\EntityRepository;

/**
 * Description of StatesApplicantsRepository
 * 
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package OPIT-HRM
 * @subpackage HiringBundle
 */
class StatesApplicantsRepository extends EntityRepository
{
    /**
     * Get the current status of an applicant
     * 
     * @param integer $applicantId applicant id
     * @return null|Opit\OpitHrm\HiringBundle\Entity\StatesApplicants
     */
    public function getCurrentStatus($applicantId)
    {
        $applicantState = $this->createQueryBuilder('sa')
            ->where('sa.applicant = :applicantId')
            ->setParameter(':applicantId', $applicantId)
            ->add('orderBy', 'sa.id DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $applicantState->getOneOrNullResult();
    }
}
