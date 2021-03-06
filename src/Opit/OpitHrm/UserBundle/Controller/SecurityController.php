<?php

/*
 *  This file is part of the OPIT-HRM project.
 * 
 *  (c) Opit Consulting Kft. <info@opit.hu>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\OpitHrm\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Opit\OpitHrm\UserBundle\Entity\User;
use Opit\OpitHrm\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Opit\OpitHrm\UserBundle\Entity\UserRepository;

/**
 * Description of SecurityController
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package OPIT-HRM
 * @subpackage UserBundle
 */
class SecurityController extends Controller
{
    /**
     * @Route("/secured/login", name="OpitOpitHrmUserBundle_security_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $form = $this->createForm(new UserType());
        
        $request = $this->getRequest();
        $session = $request->getSession();
        
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
            'form' => $form->createView()
        );
    }
    
    /**
     * @Route("/secured/login_check", name="OpitOpitHrmUserBundle_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/secured/logout", name="OpitOpitHrmUserBundle_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}
