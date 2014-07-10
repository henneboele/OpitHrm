<?php

/*
 *  This file is part of the {Bundle}.
 *
 *  (c) Opit Consulting Kft. <info@opit.hu>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\Notes\LeaveBundle\Model;

/**
 * Leave Entitlement interface.
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package Notes
 * @subpackage HolidayBundle
 */
interface LeaveEntitlementEmployeeInterface
{
    public function getDateOfBirth();
    public function getJoiningDate();
    public function getNumberOfChildren();
}
