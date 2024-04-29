<?php
namespace App\Controller;

use App\Entity\RequestLeave;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\LeaveType;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\LeaveBalance;



class RequestLeaveController extends AbstractController
{
    /**
     * @Route("/employe/request-leave", name="employe_request_leave", methods={"POST"})
     */
    public function requestLeave(Request $request, EntityManagerInterface $entityManager): Response
    {
        // check if the user is logged in
        $userId = $request->getSession()->get('user')->getId();
        if (!$userId) {
            return $this->redirectToRoute('auth_page');
        }

        // Fetch the user from the database
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        // // Create a new leave request
        // if ($request->isMethod('POST')) {
        //     $startDate = new \DateTime($request->request->get('startdate'));
        //     $endDate = new \DateTime($request->request->get('enddate'));
        //     $daysRequested = $startDate->diff($endDate)->days + 1; // +1 to include both start and end dates

        //     // Check if the user has enough annual balance
        //     if ($user->getAnnualBalance() < $daysRequested) {
        //         $this->addFlash('error', 'Your annual balance is less than the requested leave days.');
        //         return $this->redirectToRoute('employe_index');
        //     }

        //     // Find the selected leave type
        //     $leaveTypeRepository = $entityManager->getRepository(LeaveType::class);
        //     $leaveType = $leaveTypeRepository->find($request->request->get('inquiry'));

        //     // Check if the user has enough remaining leave days for the selected type
        //     $leaveBalanceRepository = $entityManager->getRepository(LeaveBalance::class);
        //     $leaveBalance = $leaveBalanceRepository->findOneBy(['user' => $user, 'leaveType' => $leaveType]);
        //     if ($leaveBalance->getRemaining() < $daysRequested) {
        //         $this->addFlash('error', 'Your remaining leave days for the selected type are less than the requested leave days.');
        //         return $this->redirectToRoute('employe_index');
        //     } hetha shih just bch njareb li tahtou bch nbdl les messages d'erreur fi wost form mtaa request mch f login
        // Create a new leave request
if ($request->isMethod('POST')) {
    $startDate = new \DateTime($request->request->get('startdate'));
    $endDate = new \DateTime($request->request->get('enddate'));
    $daysRequested = $startDate->diff($endDate)->days + 1; // +1 to include both start and end dates

    // Check if the user has enough annual balance
    if ($user->getAnnualBalance() < $daysRequested) {
        $errorMessage = 'Your annual balance is less than the requested leave days.';

        $requestLeaves = $entityManager->getRepository(RequestLeave::class)->findBy(['user' => $user]);
        $leaveBalanceRepository = $entityManager->getRepository(LeaveBalance::class);
        $leaveBalances = $leaveBalanceRepository->findBy(['user' => $user]);
        $leaveTypeRepository = $entityManager->getRepository(LeaveType::class);
        $leaveTypes = $leaveTypeRepository->findAll();
        return $this->render('leave_request_form.html.twig', [
            'requestLeaves' => $requestLeaves,
            'errorMessage' => $errorMessage,
            'leaveBalances' => $leaveBalances, 
            'annualBalance' => $user->getAnnualBalance(),
            'userInfo' => $user, 
            'leaveTypes' => $leaveTypes, 
        ]);
    }

    // Find the selected leave type
    $leaveTypeRepository = $entityManager->getRepository(LeaveType::class);
    $leaveType = $leaveTypeRepository->find($request->request->get('inquiry'));

    // Check if the user has enough remaining leave days for the selected type
    $leaveBalanceRepository = $entityManager->getRepository(LeaveBalance::class);
    $leaveBalance = $leaveBalanceRepository->findOneBy(['user' => $user, 'leaveType' => $leaveType]);
    if ($leaveBalance->getRemaining() < $daysRequested) {
        $errorMessage = 'Your remaining leave days for the selected type are less than the requested leave days.';

        $requestLeaves = $entityManager->getRepository(RequestLeave::class)->findBy(['user' => $user]);
        $leaveBalances = $leaveBalanceRepository->findBy(['user' => $user]); // Fetch leaveBalances
        $leaveTypes = $entityManager->getRepository(LeaveType::class)->findAll(); // Fetch leaveTypes

        return $this->render('leave_request_form.html.twig', [
            'requestLeaves' => $requestLeaves,
            'errorMessage' => $errorMessage,
            'leaveBalances' => $leaveBalances, // Pass leaveBalances to the template
            'annualBalance' => $user->getAnnualBalance(), // Pass annualBalance to the template
            'userInfo' => $user, // Pass userInfo to the template
            'leaveTypes' => $leaveTypes, // Pass leaveTypes to the template
        ]);
    }


            $leaveRequest = new RequestLeave();
            $leaveRequest->setUser($user);
            $leaveRequest->setStartDate($startDate);
            $leaveRequest->setEndDate($endDate);
            $leaveRequest->setReason($request->request->get('reason'));
            $leaveRequest->setLeaveType($leaveType);

            // Save the leave request
            $entityManager->persist($leaveRequest);
            $entityManager->flush();
            // Add a successful message
            $this->addFlash('success', 'Your leave request has been submitted successfully.');
            // Redirect the user back to the index page
            return $this->redirectToRoute('employe_index_leave_request');
        }

        $requestLeaves = $entityManager->getRepository(RequestLeave::class)->findBy(['user' => $user]);

        return $this->render('leave_request_form.html.twig', [
            'requestLeaves' => $requestLeaves,
        ]);
    }
    /**
     * @Route("/employe/approve/{id}", name="employe_approve_leave", methods={"POST"})
     */
    public function approveLeave($id, EntityManagerInterface $entityManager): Response
    {
        $leaveRequest = $entityManager->getRepository(RequestLeave::class)->find($id);

        if (!$leaveRequest) {
            throw $this->createNotFoundException('No leave request found for id '.$id);
        }

        // Get the user and the leave type of the request
        $user = $leaveRequest->getUser();
        $leaveType = $leaveRequest->getLeaveType();

        // Calculate the number of leave days
        $startDate = $leaveRequest->getStartDate();
        $endDate = $leaveRequest->getEndDate();
        $daysRequested = $startDate->diff($endDate)->days + 1; // +1 to include both start and end dates

        // Check if the user has enough annual balance
        if ($user->getAnnualBalance() < $daysRequested) {
            $this->addFlash('error', 'The remaining annual balance is not enough to approve this leave request.');
            return $this->redirectToRoute('tables');
        }

        $leaveBalanceRepository = $entityManager->getRepository(LeaveBalance::class);
        $leaveBalance = $leaveBalanceRepository->findOneBy(['user' => $user, 'leaveType' => $leaveType]);

        // Check if the user has enough leave balance
        if ($leaveBalance->getRemaining() < $daysRequested) {
            $this->addFlash('error', 'The remaining leave balance for this leave type is not enough to approve this leave request.');
            return $this->redirectToRoute('tables');
        }

        // Decrease the annual balance and the remaining leave days of the selected type
        $user->setAnnualBalance($user->getAnnualBalance() - $daysRequested);
        $entityManager->persist($user); // Add this line

        $leaveBalance->setRemaining($leaveBalance->getRemaining() - $daysRequested);
        $entityManager->persist($leaveBalance); // Add this line

        // Increase the consumed days
        $newConsumed = $leaveBalance->getConsumed() + $daysRequested;
        if ($newConsumed < 0) {
            return new Response('Error: Consumed leave balance cannot be negative.');
        }
        $leaveBalance->setConsumed($newConsumed);

        $leaveRequest->setStatus('approved');
        $entityManager->flush();
            return $this->redirectToRoute('tables');
    }
    /**
     * @Route("/employe/reject/{id}", name="employe_reject_leave", methods={"POST"})
     */
    public function rejectLeave($id, EntityManagerInterface $entityManager): Response
    {
        $leaveRequest = $entityManager->getRepository(RequestLeave::class)->find($id);

        if (!$leaveRequest) {
            throw $this->createNotFoundException('No leave request found for id '.$id);
        }

        $leaveRequest->setStatus('rejected');
        $entityManager->flush();

        return $this->redirectToRoute('tables');
    }
#

/**
 * @Route("/admin/leave-requests", name="admin_leave_requests")
 */
public function leaveRequests(Request $request, EntityManagerInterface $entityManager): Response
{
    // check if the user is logged in
    $userId = $request->getSession()->get('user')->getId();
    if (!$userId) {
        return $this->redirectToRoute('auth_page');
    }

    // Fetch the user from the database
    $userRepository = $entityManager->getRepository(User::class);
    $user = $userRepository->find($userId);

    // Fetch all leave requests
    $allRequests = $entityManager->getRepository(RequestLeave::class)->findAll();

    // Filter out the current user's requests
    $requestLeaves = array_values(array_filter($allRequests, function($request) use ($user) {
        return $request->getUser()->getId() !== $user->getId();
    }));

    return $this->render('admin-dashboard/tables.html.twig', [
        'requestLeaves' => $requestLeaves,
    ]);
}
}
?>