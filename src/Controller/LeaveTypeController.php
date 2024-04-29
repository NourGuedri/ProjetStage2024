<?php

namespace App\Controller;

use App\Entity\LeaveType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\LeaveBalance;
use App\Entity\User;

class LeaveTypeController extends AbstractController
{
    /**
     * @Route("/leave-type/create", name="leave_type_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve form data
        $leaveTypeName = $request->request->get('name');
        $leaveTypeDescription = $request->request->get('description');
        $leaveTypeMaxDays = $request->request->get('max_days');

        // Check if a leave type with the same name already exists
        $leaveTypeRepository = $entityManager->getRepository(LeaveType::class);
        $existingLeaveType = $leaveTypeRepository->findOneBy(['name' => $leaveTypeName]);
        if ($existingLeaveType) {
            $this->addFlash('name-error', 'This leave type name is already in use. Please choose a different name.');
            return $this->redirectToRoute('charts');
        }

        // Create a new leave type object
        $leaveType = new LeaveType($leaveTypeDescription, $leaveTypeName, $leaveTypeMaxDays);

        // Save the leave type to the database
        try {
            $entityManager->persist($leaveType);
            $entityManager->flush();

            // Fetch all users
            // ...

            $users = $entityManager->getRepository(User::class)->findAll();

            // Loop through all users
            foreach ($users as $user) {
                // Create a new LeaveBalance for each user with the new leave type
                $leaveBalance = new LeaveBalance();
                $leaveBalance->setUser($user);
                $leaveBalance->setLeaveType($leaveType);
                $leaveBalance->setConsumed(0);
                $leaveBalance->setRemaining($leaveTypeMaxDays);

                // Save the changes to the database
                $entityManager->persist($leaveBalance);
            }

            // Flush once after all leave balances have been created
            $entityManager->flush();

            // Add a flash message
            $this->addFlash('success', 'Leave type creation successful.');

            // Redirect to the leave type index page
            return $this->redirectToRoute('charts');
        } catch (\Exception $e) {
            // Handle database or other errors
            $this->addFlash('error', 'An error occurred while creating the leave type. Please try again later.');

            // Redirect back to leave type creation page with error message
            return $this->redirectToRoute('charts');
        }
    }

    /**
 * @Route("/leave-types", name="leave_types")
 */
public function leaveTypes(EntityManagerInterface $entityManager)
{
    $leaveTypes = $entityManager->getRepository(LeaveType::class)->findAll();

    return $this->render('admin-dashboard/deletetype.html.twig', [
        'leaveTypes' => $leaveTypes,
    ]);
}


public function delete(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    try {
        // Retrieve the leave type
        $leaveType = $entityManager->getRepository(LeaveType::class)->find($id);

        // Check if the leave type exists
        if (!$leaveType) {
            $this->addFlash('error', 'This leave type does not exist.');
            return $this->redirect($request->headers->get('referer'));
        }

        // Delete the leave type
        $entityManager->remove($leaveType);
        $entityManager->flush();

        // Add a flash message
        $this->addFlash('success', 'Leave type deletion successful.');

        // Redirect to the same page
        return $this->redirect($request->headers->get('referer'));
    } catch (\Exception $e) {
        // Handle database or other errors
        $this->addFlash('error', 'An error occurred while deleting the leave type. Please try again later.');

        // Redirect back to the same page with error message
        return $this->redirect($request->headers->get('referer'));
    }
}

    /**
     * @Route("/leave-type/modify/{id}", name="leave_type_modify", methods={"GET","POST"})
     */
    public function modify(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $leaveType = $entityManager->getRepository(LeaveType::class)->find($id);
        $leaveTypes = $entityManager->getRepository(LeaveType::class)->findAll(); // Fetch all leave types

        if (!$leaveType) {
            $this->addFlash('error', 'This leave type does not exist.');
            return $this->redirectToRoute('deletetype');
        }

        if ($request->isMethod('POST')) {
            // Retrieve form data
            $leaveTypeName = $request->request->get('name');
            $leaveTypeDescription = $request->request->get('description');
            $leaveTypeMaxDays = $request->request->get('max_days');

            // Update the leave type
            $leaveType->setName($leaveTypeName);
            $leaveType->setDescription($leaveTypeDescription);
            $leaveType->setMaxDays($leaveTypeMaxDays);

            // Save the changes to the database
            try {
                $entityManager->flush();

                // Add a flash message
                $this->addFlash('success', 'Leave type modification successful.');

                // Redirect to the leave type index page
                return $this->redirectToRoute('deletetype');
            } catch (\Exception $e) {
                // Handle database or other errors
                $this->addFlash('error', 'An error occurred while modifying the leave type. Please try again later.');

                // Redirect back to leave type modification page with error message
                return $this->redirectToRoute('leave_type_modify', ['id' => $id]);
            }
        }

        return $this->render('admin-dashboard/deletetype.html.twig', [
            'leaveType' => $leaveType,
            'leaveTypes' => $leaveTypes,
        ]);
    }
}