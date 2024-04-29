<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\RequestLeave;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\LeaveType;
use App\Entity\LeaveBalance;
use Symfony\Component\HttpFoundation\JsonResponse;


class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        // check if the user is in hr dept
        $user = $request->getSession()->get('user');

        if(!$user->getIsHr()){
            return $this->redirectToRoute('employe_index');
        }
        $query = $entityManager->createQuery(
            'SELECT lt.name, COUNT(r.id) as count
            FROM App\Entity\RequestLeave r
            JOIN r.leaveType lt
            GROUP BY lt.id'
        );

        $leaveTypeCounts = $query->getResult();
        // Get the user count
        $userCount = $entityManager->getRepository(User::class)->count([]);
        // Get the count of pending requests
        $pendingRequestsCount = $entityManager->getRepository(RequestLeave::class)->count(['status' => 'Pending']);
        // Get the current date and time
        $now = new \DateTime();

        // Get the first day of the current month
        $firstDayOfMonth = $now->modify('first day of this month midnight');

        // Get the count of leave requests created this month
        $monthlyLeaveRequests = $entityManager->getRepository(RequestLeave::class)->createQueryBuilder('r')
            ->andWhere('r.startDate >= :firstDayOfMonth')
            ->setParameter('firstDayOfMonth', $firstDayOfMonth)
            ->getQuery()
            ->getScalarResult();

        $monthlyLeaveRequestsCount = count($monthlyLeaveRequests);
        // Get the first day of the current year
        $firstDayOfYear = (new \DateTime())->setDate($now->format('Y'), 1, 1)->setTime(0, 0, 0);

        // Get the count of leave requests created this year
        $annualLeaveRequests = $entityManager->getRepository(RequestLeave::class)->createQueryBuilder('r')
            ->andWhere('r.startDate >= :firstDayOfYear')
            ->setParameter('firstDayOfYear', $firstDayOfYear)
            ->getQuery()
            ->getScalarResult();

        $annualLeaveRequestsCount = count($annualLeaveRequests);

        // Get the connection
        $connection = $entityManager->getConnection();

        // Create a query builder
        $queryBuilder = $connection->createQueryBuilder();

        // Build the query
        $queryBuilder
            ->select('MONTH(r.start_date) as month', 'COUNT(r.id) as count')
            ->from('request_leave', 'r') // Use the actual table name here
            ->where('YEAR(r.start_date) = :year')
            ->groupBy('month')
            ->setParameter('year', date('Y'));

        // Execute the query
        $stmt = $queryBuilder->execute();

        // Fetch the results
        $leaveRequestsByMonth = $stmt->fetchAll();

        // Convert the result to an associative array with the month as the key
        $leaveRequestsByMonth = array_column($leaveRequestsByMonth, 'count', 'month');

        // Ensure that the array has entries for all months
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($leaveRequestsByMonth[$i])) {
                $leaveRequestsByMonth[$i] = 0;
            }
        }

        // Sort the array by the month
        ksort($leaveRequestsByMonth);

        // Convert the associative array to a zero-indexed array
        $leaveRequestsByMonth = array_values($leaveRequestsByMonth);

        $rejectedRequestsCount = $entityManager->getRepository(RequestLeave::class)->count(['status' => 'Rejected']);
        $approvedRequestsCount = $entityManager->getRepository(RequestLeave::class)->count(['status' => 'Approved']);

        // Now you can pass $leaveTypeCounts to your view or use it as needed
        // ...

        return $this->render('admin-dashboard/index.html.twig', [
            'leaveTypeCounts' => $leaveTypeCounts,
            'userCount' => $userCount,
            'pendingRequestsCount' => $pendingRequestsCount,
            'monthlyLeaveRequestsCount' => $monthlyLeaveRequestsCount,
            'annualLeaveRequestsCount' => $annualLeaveRequestsCount,
            'leaveRequestsByMonth' => $leaveRequestsByMonth,
            'rejectedRequestsCount' => $rejectedRequestsCount,
            'approvedRequestsCount' => $approvedRequestsCount,
        ]);
    }
    /**
     * @Route("/dashboard/tables", name="tables", methods={"GET"})
     */
    public function tables(Request $request, EntityManagerInterface $entityManager): Response
    {
        // get the current SESSION user
        $userId = $request->getSession()->get('user')->getId();
        if (!$userId) {
            return $this->redirectToRoute('auth_page');
        }



        // create a query builder
        $queryBuilder = $entityManager->createQueryBuilder();

        // build the query
        $queryBuilder
            ->select('r')
            ->from('App\Entity\RequestLeave', 'r')
            ->join('r.leaveType', 'lt')
            ->join('r.user', 'u')
            ->where('u.id != :userId')
            ->andWhere('r.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('status', 'pending');


        // execute the query and get the results
        $requestLeaves = $queryBuilder->getQuery()->getResult();

        // dump the results (for debugging)

        // Pass the leave requests to your template
        return $this->render('admin-dashboard/tables.html.twig', [
            'requestLeaves' => $requestLeaves,
        ]);
    }

    /**
     * @Route("/charts", name="charts")
     */
    public function charts(EntityManagerInterface $entityManager)
{
    // $leaveTypes = $entityManager->getRepository(LeaveType::class)->findAll();
        return $this->render('admin-dashboard/charts.html.twig');
    }

    /**
     * @Route("/deletetype", name="deletetype")
     */
    public function deletetype(EntityManagerInterface $entityManager)
{
    $leaveTypes = $entityManager->getRepository(LeaveType::class)->findAll();
        return $this->render('admin-dashboard/deletetype.html.twig', [
            'leaveTypes' => $leaveTypes,
        ]);
    }

// /**
//  * @Route("/create-leave-balance", name="create_leave_balance")
//  */
// public function createLeaveBalance(Request $request, EntityManagerInterface $entityManager)
// {
//     // Assuming you have a User entity, a LeaveBalance entity, and a LeaveType entity
//     // Get the user ID and leave type ID from the request
//     $user_id = $request->request->get('user_id');
//     $leave_type_id = $request->request->get('leave_type_id');
//     // Fetch the user and leave type
//     $user = $entityManager->getRepository(User::class)->find($user_id); // replace $user_id with the actual user ID
//     $leaveType = $entityManager->getRepository(LeaveType::class)->find($leave_type_id); // replace $leave_type_id with the actual leave type ID

//     if (!$user || !$leaveType) {
//         // Handle error: user or leave type does not exist
//     }


//     // Calculate consumed and remaining days
//     $consumed = 0; // This should be calculated based on the user's leave records
//     $remaining = $leaveType->getMaxDays() - $consumed;

//     // Create a new LeaveBalance instance
//     $leaveBalance = new LeaveBalance();
//     $leaveBalance->setUser($user);
//     $leaveBalance->setLeaveType($leaveType);
//     $leaveBalance->setConsumed($consumed);
//     $leaveBalance->setRemaining($remaining);

//     // Save the changes to the database
//     $entityManager->persist($leaveBalance);
//     try {
//         $entityManager->flush();
//     } catch (\Exception $e) {
//         // Handle database or other errors
//     }

//     return $this->redirectToRoute('leavebalance');
// }  //hethi nahitha khtr kasamtha w hatitha f functions okhrin ....


    /**
     * @Route("/leavebalance", name="leavebalance")
     */
    public function leavebalance(Request $request, EntityManagerInterface $entityManager)
    {
        // check if the user is logged in
        $userId = $request->getSession()->get('user')->getId();
        if (!$userId) {
            return $this->redirectToRoute('auth_page');
        }

        // Fetch all users except the admin
        $query = $entityManager->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.id != :adminId')
            ->setParameter('adminId', $userId)
            ->getQuery();

        $users = $query->getResult();

        // Fetch all leave balances
        $leaveBalances = $entityManager->getRepository(LeaveBalance::class)->findAll();

        return $this->render('admin-dashboard/leavebalance.html.twig', [
            'users' => $users,
            'leaveBalances' => $leaveBalances,
        ]);
    }


/**
 * @Route("/edit-annual-balance/{id}", name="edit_annual_balance", methods={"POST"})
 */
public function editAnnualBalance(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(User::class)->find($id);

    if (!$user) {
        $this->addFlash('error', 'This user does not exist.');
        return $this->redirectToRoute('leavebalance');
    }

    if ($request->isMethod('POST')) {
        // Retrieve form data
        $annualBalance = $request->request->get('annual_balance');

        // Update the user's annual balance
        $user->setAnnualBalance($annualBalance);

        // Save the changes to the database
        try {
            $entityManager->flush();
            // Add a flash message
            $this->addFlash('success', 'Annual balance modification successful.');
            // Redirect to the leave balance page
            return $this->redirectToRoute('leavebalance');
        } catch (\Exception $e) {
            // Log the exception message
            error_log($e->getMessage());
            // Handle database or other errors
            $this->addFlash('error', 'An error occurred while modifying the annual balance. Please try again later.');
            // Redirect back to leave balance page with error message
            return $this->redirectToRoute('leavebalance');
        }
    }

    return $this->render('admin-dashboard/leavebalance.html.twig', [
        'user' => $user,
    ]);
}
}
