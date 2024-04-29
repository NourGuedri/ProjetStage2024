<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\LeaveType;
use App\Entity\RequestLeave;
use App\Entity\User;
use App\Entity\LeaveBalance;



class EmployeController extends AbstractController
{
    /**
     * @Route("/employe/index", name="employe_index")
     */

     public function index(Request $request, EntityManagerInterface $entityManager): Response
     {
         // check if the user is logged in
         $user = $request->getSession()->get('user');
         if (!$user) {
             return $this->redirectToRoute('auth_page');
         }
         $userInfo = $user->consulterProfil();
     
         // Fetch all leave types
         $leaveTypeRepository = $entityManager->getRepository(LeaveType::class);
         $leaveTypes = $leaveTypeRepository->findAll();
         // fetch all Request leaves
         $requestLeaveRepository = $entityManager->getRepository(RequestLeave::class);
         $requestLeaves = $requestLeaveRepository->findBy(['user' => $user]);
         // Fetch the leave balances for the current user
        $leaveBalanceRepository = $entityManager->getRepository(LeaveBalance::class);
         $leaveBalances = $leaveBalanceRepository->findBy(['user' => $user]);
         $annualBalance = $user->getAnnualBalance();
         $sessionUser = $request->getSession()->get('user');

         return $this->render('index.html.twig', [
             'userInfo' => $userInfo,
             'leaveTypes' => $leaveTypes,
             'requestLeaves' => $requestLeaves,
             'leaveBalances' => $leaveBalances,
             'annualBalance' => $annualBalance,
             'user' => $sessionUser,

         ]);
     }
    
    
    /**
     * @Route("/lucky/number", name="lucky_number")
     */
    public function number(): Response
    {
        $number = random_int(0, 100);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
}
/**
 * @Route("/employe/index_update_profile", name="employe_index_update_profile")
 */
public function indexUpdateProfile(Request $request, EntityManagerInterface $entityManager): Response
{
    // check if the user is logged in
    $user = $request->getSession()->get('user');
    if (!$user) {
        return $this->redirectToRoute('auth_page');
    }
    $userInfo = $user->consulterProfil();

    // Fetch all leave types
    $leaveTypeRepository = $entityManager->getRepository(LeaveType::class);
    $leaveTypes = $leaveTypeRepository->findAll();
    // fetch all Request leaves
    $requestLeaveRepository = $entityManager->getRepository(RequestLeave::class);
    $requestLeaves = $requestLeaveRepository->findBy(['user' => $user]);
    // Fetch the leave balances for the current user
    $leaveBalanceRepository = $entityManager->getRepository(LeaveBalance::class);
    $leaveBalances = $leaveBalanceRepository->findBy(['user' => $user]);
    $annualBalance = $user->getAnnualBalance();
    // Get the user from the session
    $sessionUser = $request->getSession()->get('user');
    return $this->render('profile_update_form.html.twig', [
        'userInfo' => $userInfo,
        'leaveTypes' => $leaveTypes,
        'requestLeaves' => $requestLeaves,
        'leaveBalances' => $leaveBalances,
        'annualBalance' => $annualBalance,
        'user' => $sessionUser,
    ]);
}

/**
 * @Route("/employe/index_leave_request", name="employe_index_leave_request")
 */
public function indexLeaveRequest(Request $request, EntityManagerInterface $entityManager): Response
{
    // check if the user is logged in
    $user = $request->getSession()->get('user');
    if (!$user) {
        return $this->redirectToRoute('auth_page');
    }
    $userInfo = $user->consulterProfil();

    // Fetch all leave types
    $leaveTypeRepository = $entityManager->getRepository(LeaveType::class);
    $leaveTypes = $leaveTypeRepository->findAll();
    // fetch all Request leaves
    $requestLeaveRepository = $entityManager->getRepository(RequestLeave::class);
    $requestLeaves = $requestLeaveRepository->findBy(['user' => $user]);
    // Fetch the leave balances for the current user
    $leaveBalanceRepository = $entityManager->getRepository(LeaveBalance::class);
    $leaveBalances = $leaveBalanceRepository->findBy(['user' => $user]);
    $annualBalance = $user->getAnnualBalance();
    // Get the user from the session
    $sessionUser = $request->getSession()->get('user');
    return $this->render('leave_request_form.html.twig', [
        'userInfo' => $userInfo,
        'leaveTypes' => $leaveTypes,
        'requestLeaves' => $requestLeaves,
        'leaveBalances' => $leaveBalances,
        'annualBalance' => $annualBalance,
        'user' => $sessionUser,
    ]);
}
    


}
?>