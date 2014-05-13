<?php

namespace Opit\Notes\HolidayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Doctrine\Common\Collections\ArrayCollection;
use Opit\Notes\HolidayBundle\Form\LeaveRequestType;
use Opit\Notes\HolidayBundle\Entity\LeaveRequest;

class HolidayController extends Controller
{
    /**
     * To list leaves in Notes
     *
     * @Route("/secured/leave/list", name="OpitNotesHolidayBundle_leave_list")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function listLeaveRequestsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $securityContext = $this->container->get('security.context');
        $employee = $securityContext->getToken()->getUser()->getEmployee();
        $isSearch = $request->request->get('issearch');
        $searchRequests = array();
        
        $config = $this->container->getParameter('opit_notes_holiday');
        $maxResults = $config['max_results'];
        $offset = $request->request->get('offset');
        $pagnationParameters = array(
            'firstResult' => ($offset * $maxResults),
            'maxResults' => $maxResults,
            'isAdmin' => $securityContext->isGranted('ROLE_ADMIN'),
            'employee' => $employee
        );
        
        if ($isSearch) {
            $searchRequests = $request->request->get('search');
        }
        
        $leaveRequests = $entityManager->getRepository('OpitNotesHolidayBundle:LeaveRequest')
            ->findAllByFiltersPaginated($pagnationParameters, $searchRequests);
        
        if ($request->request->get('resetForm') || $isSearch || null !== $offset) {
            $template = 'OpitNotesHolidayBundle:Holiday:_list.html.twig';
        } else {
            $template = 'OpitNotesHolidayBundle:Holiday:list.html.twig';
        }
        
        return $this->render(
            $template,
            array(
                'leaveRequests' => $leaveRequests,
                'numberOfPages' => ceil(count($leaveRequests) / $maxResults),
                'offset' => ($offset + 1),
                'maxPages' => $config['max_pager_pages']
            )
        );
    }
    
    /**
     * To add/edit leave in Notes
     *
     * @Route("/secured/leave/show/{id}", name="OpitNotesHolidayBundle_leave_show", defaults={"id" = "new"}, requirements={ "id" = "new|\d+"})
     * @Secure(roles="ROLE_USER")
     * @throws CreateNotFoundException
     * @Template()
     */
    public function showLeaveRequestAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $leaveRequestId = $request->attributes->get('id');
        $isNewLeaveRequest = 'new' === $leaveRequestId ? true : false;
        $securityContext = $this->container->get('security.context');
        $token = $securityContext->getToken();
        
        if ($isNewLeaveRequest) {
            $employee = $token->getUser()->getEmployee();
            $leaveRequest = new LeaveRequest();
            $leaveRequest->setEmployee($employee);
        } else {
            $leaveRequest = $entityManager->getRepository('OpitNotesHolidayBundle:LeaveRequest')->find($leaveRequestId);
            
            if (null === $leaveRequest) {
                throw $this->createNotFoundException('Missing leave request.');
            }
            
            if ($token->getUser()->getEmployee() !== $leaveRequest->getEmployee() && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException(
                    'Access denied for leave request ' . $leaveRequest->getLeaveRequestId()
                );
            }
        }
        
        $children = new ArrayCollection();
        $form = $this->createForm(
            new LeaveRequestType($isNewLeaveRequest),
            $leaveRequest,
            array('em' => $entityManager, 'validation_groups' => array('user'))
        );
        
        if (null !== $leaveRequest) {
            foreach ($leaveRequest->getLeaves() as $leave) {
                $children->add($leave);
            }
        }
                
        if ($request->isMethod("POST")) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                foreach ($children as $child) {
                    if (false === $leaveRequest->getLeaves()->contains($child)) {
                        $child->setLeaveRequest();
                        $entityManager->remove($child);
                    }
                }
                
                $entityManager->persist($leaveRequest);
                $entityManager->flush();
                
                return $this->redirect($this->generateUrl('OpitNotesHolidayBundle_leave_list'));
            }
        }
        
        return $this->render(
            'OpitNotesHolidayBundle:Holiday:showLeaveRequest.html.twig',
            array('form' => $form->createView(), 'isNewLeaveRequest' => $isNewLeaveRequest)
        );
    }

    /**
     * To delete leave request in Notes
     *
     * @Route("/secured/leaverequest/delete", name="OpitNotesHolidayBundle_leaverequest_delete")
     * @Secure(roles="ROLE_USER")
     * @throws AccessDeniedException
     * @Template()
     * @Method({"POST"})
     */
    public function deleteLeaveRequestAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        $token = $securityContext->getToken();
        $entityManager = $this->getDoctrine()->getManager();
        $ids = $request->request->get('id');
        
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        
        foreach ($ids as $id) {
            $leaveRequest = $entityManager->getRepository('OpitNotesHolidayBundle:LeaveRequest')->find($id);

            if ($token->getUser()->getEmployee() !== $leaveRequest->getEmployee() &&
                !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException(
                    'Access denied for leave.'
                );
            }
            $entityManager->remove($leaveRequest);
        }
        
        $entityManager->flush();
        
        return new JsonResponse('$userNames');
    }
}
