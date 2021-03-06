<?php

/*
 *  This file is part of the {Bundle}.
 *
 *  (c) Opit Consulting Kft. <info@opit.hu>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\OpitHrm\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package OPIT-HRM
 * @subpackage Command
 */
class UserACLCommand extends ContainerAwareCommand
{
    private $em;
    private $output;
    private $adminRoles = array('ROLE_ADMIN', 'ROLE_GENERAL_MANAGER', 'ROLE_TEAM_MANAGER');
    private $aclManager;

    protected function configure()
    {
        $this->setName('opithrm:permission:fix-user-acl')
            ->setDescription('Set missing permission for users based on assigned roles.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->aclManager = $this->getContainer()->get('opit.security.acl.manager');

        $this->setUserACL();

        $this->output->writeln('done');
    }

    /**
     * Set ACL for user objects that do not have it set
     */
    protected function setUserACL()
    {
        $users = $this->em->getRepository('OpitOpitHrmUserBundle:User')->findAll();

        foreach($users as $user) {
            if (!$this->aclManager->findSecurityIdenties($user)) {

                // Add or update owner access to user object
                $role = $this->em->getRepository('OpitOpitHrmUserBundle:Groups')
                    ->findOneByRole($this->getSecurityRole($user));

                $this->aclManager->grant($user, $role);
            }
        }
    }

    /**
     * Get security role depending on the roles of the user
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return string
     */
    protected function getSecurityRole(UserInterface $user)
    {
        $securityRole = 'ROLE_SYSTEM_ADMIN';

        foreach ($user->getRoles() as $role) {
            if (in_array($role, $this->adminRoles)) {
                $securityRole = 'ROLE_ADMIN';
                break;
            }
        }

        return $securityRole;
    }
}