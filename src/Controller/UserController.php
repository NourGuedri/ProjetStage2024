<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\LeaveBalance;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserController extends AbstractController
{
    // /**
    //  * @Route("/profile_update", name="profile_update", methods={"POST"})
    //  */
    // public function profileUpdate(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     // Get the user from the session
    //     $sessionUser = $request->getSession()->get('user');

    //     // If the user is not logged in, redirect to the login page
    //     if (!$sessionUser) {
    //         return $this->redirectToRoute('login');
    //     } else {
    //         // Retrieve the user from the database
    //         $user = $entityManager->getRepository(User::class)->find($sessionUser->getId());

    //         // Get the form data
    //         $username = $request->request->get('username');
    //         $department = $request->request->get('department');
    //         $email = $request->request->get('email');


    //         // Update the user's information
    //         $user->setUserName($username);
    //         $user->setDepartment($department);
    //         $user->setEmail($email);

    //         // Save the changes to the database
    //         $entityManager->persist($user);
    //         $entityManager->flush();


    //         // Update the user in the session
    //         $request->getSession()->set('user', $user);

    //         // changing password
    //         $old_password = $request->request->get('old_password');
    //         $new_password = $request->request->get('new_password');
    //         if ($old_password != "" && $new_password != "") {
    //             // compare , if not valid return error flash
    //             if (!password_verify($old_password, $user->getPassword())) {
    //                 $this->addFlash('password_changing_error', 'The old password is incorrect');
    //                 return $this->redirectToRoute('employe_index');
    //             }
    //             $user->setPassword(password_hash($new_password, PASSWORD_BCRYPT));
    //         }

    //         //resave
    //         $entityManager->persist($user);
    //         $entityManager->flush();
    //         $request->getSession()->set('user', $user);


    //         // Redirect to a success page
    //         return $this->redirectToRoute('employe_index');
    //     }

    // }

    /**
 * @Route("/employe_update_profile", name="employe_update_profile", methods={"GET"})
 */
    public function employeUpdateProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the user from the session
        $sessionUser = $request->getSession()->get('user');

        // If the user is not logged in, redirect to the login page
        if (!$sessionUser) {
            return $this->redirectToRoute('login');
        } else {
            // Retrieve the user from the database
            $user = $entityManager->getRepository(User::class)->find($sessionUser->getId());

            // Get the form data
            $username = $request->request->get('username');
            $department = $request->request->get('department');
            $email = $request->request->get('email');


            // Update the user's information
            $user->setUserName($username);
            $user->setDepartment($department);
            $user->setEmail($email);

            // Save the changes to the database
            $entityManager->persist($user);
            $entityManager->flush();


            // Update the user in the session
            $request->getSession()->set('user', $user);

            // changing password
            $old_password = $request->request->get('old_password');
            $new_password = $request->request->get('new_password');
            if ($old_password != "" && $new_password != "") {
                // compare , if not valid return error flash
                if (!password_verify($old_password, $user->getPassword())) {
                    $this->addFlash('password_changing_error', 'The old password is incorrect');
                    return $this->redirectToRoute('employe_index_update_profile');
                }
                $user->setPassword(password_hash($new_password, PASSWORD_BCRYPT));
                 // Add a success flash message
                $this->addFlash('password_changing_success', 'Password changed successfully');
            }

            //resave
            $entityManager->persist($user);
            $entityManager->flush();
            $request->getSession()->set('user', $user);


            // Redirect to a success page
            return $this->redirectToRoute('employe_index_update_profile');
        }

    }
    /**
 * @Route("/user/leave-balance", name="user_leave_balance")
 */
public function leaveBalance(EntityManagerInterface $entityManager)
{
    // Assuming you have a User entity and you can get the currently logged in user
    $user = $this->getUser();

    // Fetch the leave balances for the user
    $leaveBalances = $entityManager->getRepository(LeaveBalance::class)->findBy(['user' => $user]);

    // Render the leave balance view
    return $this->render('base.html.twig', [
        'leave_balances' => $leaveBalances,
    ]);
}

}
