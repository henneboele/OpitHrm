<?php

/*
 *  This file is part of the {Bundle}.
 * 
 *  (c) Opit Consulting Kft. <info@opit.hu>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\Notes\TravelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opit\Notes\TravelBundle\Form\TravelType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Opit\Notes\TravelBundle\Entity\TravelRequest;
use Opit\Notes\StatusBundle\Entity\Status;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormError;

/**
 * Description of TravelController
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package Notes
 * @subpackage TravelBundle
 */
class TravelController extends Controller
{
    /**
     * @Route("/secured/travel/list", name="OpitNotesTravelBundle_travel_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $showList = $request->request->get('showList');
        $securityContext = $this->get('security.context');
        $config = $this->container->getParameter('pager_config');
        // Disable softdeleteable filter for user entity to allow persistence
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->getFilters()->disable('softdeleteable');
        $user = $this->getUser();
        $isGeneralManager = $securityContext->isGranted('ROLE_GENERAL_MANAGER');
        $isSearch = (bool) $request->request->get('issearch');
        $offset = $request->request->get('offset');
        $pagnationParameters = array(
            'firstResult' => ($offset * $config['max_results']),
            'maxResults' => $config['max_results'],
            'currentUser' => $user,
            'isAdmin' => $securityContext->isGranted('ROLE_ADMIN'),
            'isGeneralManager' => $isGeneralManager,
        );
        
        $allRequests = array();
        if ($isSearch) {
            $allRequests = $request->request->all();
        }
        
        $travelRequests = $entityManager
            ->getRepository('OpitNotesTravelBundle:TravelRequest')
            ->findAllByFiltersPaginated($pagnationParameters, $allRequests);
        
        $listingRights = $this->get('opit.model.travel_request')
            ->setTravelRequestListingRights($travelRequests);
        $teIds = $listingRights['teIds'];
        $travelRequestStates = $listingRights['travelRequestStates'];
        $currentStatusNames = $listingRights['currentStatusNames'];
        $isLocked = $listingRights['isLocked'];
        $numberOfPages = ceil(count($travelRequests) / $config['max_results']);
        $templateVars = array(
            'travelRequests' => $travelRequests,
            'teIds' => $teIds,
            'travelRequestStates' => $travelRequestStates,
            'isLocked' => $isLocked,
            'currentStatusNames' => $currentStatusNames,
            'numberOfPages' => $numberOfPages,
            'maxPages' => $config['max_pages'],
            'offset' => ($offset + 1),
            'isFirstLogin' => $user->getIsFirstLogin(),
            'states' => $entityManager->getRepository('OpitNotesStatusBundle:Status')->getStatusNameId()
        );

        if (null === $showList && (null === $offset && !$isSearch)) {
            $template = 'OpitNotesTravelBundle:Travel:list.html.twig';
        } else {
            $template = 'OpitNotesTravelBundle:Travel:_list.html.twig';
        }
        
        return $this->render($template, $templateVars);
    }

    /**
     * To generate details form for travel requests
     *
     * @Route("/secured/travel/show/details", name="OpitNotesTravelBundle_travel_show_details")
     * @Template()
     */
    public function showDetailsAction()
    {
        $travelRequest = new TravelRequest();
        $request = $this->getRequest();
        $entityManager = $this->getDoctrine()->getManager();
        $travelRequestPreview = $request->request->get('preview');
        // Disable softdeleteable filter for user entity to allow persistence
        $entityManager->getFilters()->disable('softdeleteable');
        
        // for creating entities for the travel request preview
        if (null !== $travelRequestPreview) {
            $form = $this->createForm(new TravelType(), $travelRequest, array('em' => $entityManager));
            $form->handleRequest($request);
        } else {
            $travelRequest = $this->getTravelRequest();
        }
        
        $estimatedCosts = $this->get('opit.model.travel_expense')
            ->getTRCosts($travelRequest);
        
        $currencyConfig = $this->container->getParameter('currency_config');
        
        return array(
            'travelRequest' => $travelRequest,
            'estimatedCostsEUR' => $estimatedCosts['EUR'],
            'estimatedCostsHUF' => ceil($estimatedCosts['HUF']),
            'currencyFormat' => $currencyConfig['currency_format']
        );
    }
    
    /**
     * Method to show and edit travel request
     *
     * @Route("/secured/travel/show/{id}/{fa}", name="OpitNotesTravelBundle_travel_show", defaults={"id" = "new", "fa" = "new"}, requirements={ "id" = "new|\d+", "fa" = "new|fa" })
     * @Template()
     */
    public function showTravelRequestAction(Request $request)
    {
        $user = $this->getUser();
        $generalManager = null;
        $teamManager = null;
        $entityManager = $this->getDoctrine()->getManager();
        $travelRequestId = $request->attributes->get('id');
        $forApproval = $request->attributes->get('fa');
        $isNewTravelRequest = "new" !== $travelRequestId;
        $travelRequest = ($isNewTravelRequest) ? $this->getTravelRequest($travelRequestId) : new TravelRequest();
        $statusManager = $this->get('opit.manager.travel_status_manager');
        $currentStatus = $statusManager->getCurrentStatus($travelRequest);
        $currentStatusId = $currentStatus->getId();
        
        $isEditLocked = false;
        $editRights = $this->get('opit.model.travel_request')
            ->setEditRights($user, $travelRequest, $isNewTravelRequest, $currentStatusId);
        
        if (false === $editRights && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $isEditLocked = true;
        }
        
        if (false !== $isNewTravelRequest) {
            $travelRequestStates = $statusManager->getNextStates($currentStatus);
            $generalManager = $travelRequest->getGeneralManager()->getUsername();
            if (null !== $travelRequest->getTeamManager()) {
                $teamManager = $travelRequest->getTeamManager()->getUsername();
            }
        } else {
            $travelRequest->setUser($user);
        }
        // The next available statuses.
        $travelRequestStates[$currentStatusId] = $currentStatus->getName();
        $children = $this->get('opit.model.travel_request')->addChildNodes($travelRequest);
        // Disable softdeleteable filter for user entity to allow persistence
        $entityManager->getFilters()->disable('softdeleteable');

        $form = $this->handleForm(
            $this->setTravelRequestForm($travelRequest, $entityManager, $isNewTravelRequest),
            $request,
            $travelRequest,
            $children,
            $forApproval
        );
        
        if (true === $form) {
            return $this->redirect($this->generateUrl('OpitNotesTravelBundle_travel_list'));
        }

        $this->isAccessGranted($isNewTravelRequest, $travelRequest);
        
        return array(
            'form' => $form->createView(),
            'travelRequest' => $travelRequest,
            'travelRequestStates' => $travelRequestStates,
            'isEditLocked' => $isEditLocked ? $isEditLocked : $editRights['isEditLocked'],
            'isStatusLocked' => $editRights['isStatusLocked']
        );
    }
    
    /**
     * Method to delete one or more travel requests
     *
     * @Route("/secured/travel/delete", name="OpitNotesTravelBundle_travel_delete")
     * @Template()
     * @Method({"POST"})
     */
    public function deleteTravelRequestAction(Request $request)
    {
        $securityContext = $this->get('security.context');
        $ids = $request->request->get('id');
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        
        foreach ($ids as $id) {
            $entityManager = $this->getDoctrine()->getManager();
            $travelRequest = $this->getTravelRequest($id);
            // check if user has sufficient role to delete travel request
            if ($securityContext->isGranted('ROLE_ADMIN') ||
                true === $securityContext->isGranted('DELETE', $travelRequest)) {
                
                $travelExpense = $travelRequest->getTravelExpense();
                
                if (null !== $travelExpense) {
                    $entityManager->remove($travelExpense);
                }
                $entityManager->remove($travelRequest);
            }
        }
        
        $entityManager->flush();
        
        return new JsonResponse('0');
    }
    
    /**
     * Method to change state of travel expense
     *
     * @Route("/secured/request/state/", name="OpitNotesTravelBundle_request_state")
     * @Template()
     */
    public function changeTravelRequestStateAction(Request $request)
    {
        $statusId = $request->request->get('statusId');
        $travelRequestId = $request->request->get('travelRequestId');
        $entityManager = $this->getDoctrine()->getManager();
        $travelRequest = $entityManager->getRepository('OpitNotesTravelBundle:TravelRequest')->find($travelRequestId);
        
        return $this->get('opit.model.travel_request')
            ->changeStatus($travelRequest, $statusId);
    }
    
    /**
     * 
     * @param \Opit\Notes\TravelBundle\Entity\TravelRequest $travelRequest
     * @param EntityManager $entityManager
     * @param boolean $isNewTravelRequest
     * @return type
     */
    protected function setTravelRequestForm(TravelRequest $travelRequest, EntityManager $entityManager, $isNewTravelRequest)
    {
        $form = $this->createForm(
            new TravelType($this->get('security.context')->isGranted('ROLE_ADMIN'), $isNewTravelRequest),
            $travelRequest,
            array('em' => $entityManager)
        );
        
        return $form;
    }
    
    /**
     * 
     * @param integer $travelRequestId
     * @return \Opit\Notes\TravelBundle\Entity\TravelRequest
     * @throws CreateNotFoundException
     */
    protected function getTravelRequest($travelRequestId = null)
    {
        $request = $this->getRequest();
        $entityManager = $this->getDoctrine()->getManager();
        
        if (null === $travelRequestId) {
            $travelRequestId = $request->request->get('id');
        }
        
        $travelRequest = $entityManager->getRepository('OpitNotesTravelBundle:TravelRequest')->find($travelRequestId);
        
        if (!$travelRequest) {
            throw $this->createNotFoundException('Missing travel request for id "' . $travelRequestId . '"');
        }
        
        if (true !== $this->get('security.context')->isGranted('VIEW', $travelRequest)) {
                throw new AccessDeniedException(
                    'Access denied for travel request ' . $travelRequest->getTravelRequestId()
                );
        }
        
        return $travelRequest;
    }
    
    /**
     * 
     * @param boolean $isNewTravelRequest
     * @param \Opit\Notes\TravelBundle\Entity\TravelRequest $travelRequest
     * @throws AccessDeniedException
     */
    protected function isAccessGranted($isNewTravelRequest, TravelRequest $travelRequest)
    {
        $securityContext = $this->get('security.context');
        if ($isNewTravelRequest) {
            if (true !== $securityContext->isGranted('ROLE_ADMIN') &&
                true !== $securityContext->isGranted('VIEW', $travelRequest)) {
                throw new AccessDeniedException(
                    'Access denied for travel request ' . $travelRequest->getTravelRequestId()
                );
            }
        }
    }
    
    protected function handleForm($form, $request, $travelRequest, $children, $forApproval = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $travelRequestService = $this->get('opit.model.travel_request');
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            // Ensure the correct user is processing the travel request
            if (!$travelRequestService->validateTROwner($travelRequest)) {
                $form->addError(new FormError('Invalid employee name.'));
            }

            if ($form->isValid()) {
                $isNew = $travelRequest->getId();
                // Persist deleted destinations/accomodations
                $travelRequestService->removeChildNodes($travelRequest, $children);
                $entityManager->persist($travelRequest);
                $entityManager->flush();

                // Create initial states for new travel request.
                if (null === $isNew) {
                    // Add created status for the new travel request and then send an email.
                    $travelRequestService->changeStatus($travelRequest, Status::CREATED, true);
                    // If the TR marked for approval too then modify its status
                    if ('fa' === $forApproval) {
                        $travelRequestService->changeStatus($travelRequest, Status::FOR_APPROVAL);
                    }
                }

                return true;
            }
        }
                
        return $form;
    }

   /**
     * To send travel leave summary
     *
     * @Route("/secured/travel/employeesummary", name="OpitNotesLeaveBundle_travel_employeesummary")
     * @Template()
     */
    public function employeeTravelInfoBoardAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        //travel request info
        $totalTravelRequestCount = $em->getRepository('OpitNotesTravelBundle:TravelRequest')->findEmployeeTravelRequest($user->getID());

        $notPendingTravelRequestCount = $em
                        ->getRepository('OpitNotesTravelBundle:TravelRequest')
                        ->findEmployeeNotPendingTravelRequest($user->getID()
        );
        $pendingTravelRequestCount = $totalTravelRequestCount-$notPendingTravelRequestCount;
        
        //travel expense info
        $totalTravelExpenseCount = $em->getRepository('OpitNotesTravelBundle:TravelExpense')->findEmployeeTravelExpenseCount($user->getID());
        $notPendingTravelExpenseCount = $em
                        ->getRepository('OpitNotesTravelBundle:TravelExpense')
                        ->findEmployeeNotPendingTravelExpense($user->getID()
        );
        $pendingTravelExpenseCount = $totalTravelExpenseCount - $notPendingTravelExpenseCount;

        return $this->render('OpitNotesTravelBundle:Travel:_employeeTravelInfoBoard.html.twig',
                array('pendingTravelRequestCount' => $pendingTravelRequestCount,
                    'totalTravelRequestCount' => $totalTravelRequestCount,
                    'notPendingTravelRequestCount' => $notPendingTravelRequestCount,
                    'totalTravelExpenseCount' => $totalTravelExpenseCount,
                    'pendingTravelExpenseCount' => $pendingTravelExpenseCount,
                    'notPendingTravelExpenseCount' => $notPendingTravelExpenseCount
                    ));
    }

}
