<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


use App\Entity\Admin;
use App\Entity\LeaveType;
use App\Entity\LeaveBalance;

class AuthController extends AbstractController
{


    public function index(Request $request)
    {
        $registered = $request->query->get('registered');
        return $this->render('auth.html.twig', [
            'registered' => $registered,
        ]);
    }
    
    /**
     * @Route("/auth", name="signup", methods={"POST"})
     */
    public function signup(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve form data
        $username = $request->request->get('user_name');
        $department = $request->request->get('department');
        $dateOfBirth = new \DateTime($request->request->get('date_of_birth'));
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Check if a user with the same email already exists
        $userRepository = $entityManager->getRepository(User::class);
        $existingUserByEmail = $userRepository->findOneBy(['email' => $email]);
        if ($existingUserByEmail) {
            $this->addFlash('email-error', 'This email is already in use. Please choose a different email.');
            return $this->redirectToRoute('auth_page');
        }

        // Check if a user with the same username already exists
        $existingUserByUsername = $userRepository->findOneBy(['userName' => $username]);

        if ($existingUserByUsername) {
            $this->addFlash('username-error', 'This username is already taken. Please choose a different username.');
            return $this->redirectToRoute('auth_page');
        }

        // check if human resources department is selected
        $role = $department === 'Human Resources' ? 'admin' : 'user';
        // Create a new user object
        $user = $role === 'admin' ? new Admin() : new User();
        $user->setUserName($username);
        $user->setDepartment($department);
        $user->setDateOfBirth($dateOfBirth);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT)); // Encrypt password
        $user->setRole($role);
        $user->setAnnualBalance(); // Set initial annual balance

        // Save the user to the database
        try {
            
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Fetch all leave types
            $leaveTypes = $entityManager->getRepository(LeaveType::class)->findAll();
    
            // Loop through all leave types
            foreach ($leaveTypes as $leaveType) {
                // Create a new LeaveBalance for the new user with each leave type
                $leaveBalance = new LeaveBalance();
                $leaveBalance->setUser($user);
                $leaveBalance->setLeaveType($leaveType);
                $leaveBalance->setConsumed(0);
                $leaveBalance->setRemaining($leaveType->getMaxDays());
    
                // Save the changes to the database
                $entityManager->persist($leaveBalance);
            }
    
            // Flush once after all leave balances have been created
            $entityManager->flush();
            

            // Add a flash message
            $this->addFlash('success', 'Registration successful. Please log in.');
            return $this->redirectToRoute('auth_page', ['registered' => true]);
            
        } catch (\Exception $e) {
            // Handle database or other     
            $this->addFlash('error', 'An error occurred while signing up. Please try again later.');
            // Redirect back to signup page with error message
            return $this->redirectToRoute('auth_page');
        }
    }
//     /**
//  * @Route("/auth", name="login", methods={"POST"})
//  */
// public function login(Request $request, EntityManagerInterface $entityManager): Response
// {
//     // Get the submitted username and password
//     $username = $request->request->get('user_name');
//     $password = $request->request->get('password');

//     // Find the user by username
//     $userRepository = $entityManager->getRepository(User::class);
//     $user = $userRepository->findOneBy(['userName' => $username]);

//     if (!$user) {
//         // User not found, add a flash message and redirect back to the form
//         $this->addFlash('error', 'Invalid username or password.');
//         return $this->redirectToRoute('auth_page');
//     }

//     // Check the submitted password
//     if (!password_verify($password, $user->getPassword())) {
//         // Invalid password, add a flash message and redirect back to the form
//         $this->addFlash('error', 'Invalid username or password.');
//         return $this->redirectToRoute('auth_page');
//     }

//     // Login successful, start the session and redirect to the index
//     $request->getSession()->set('user', $user);
//     return $this->redirectToRoute('employe_index');
// } //hethi function login ama kif noghlit f osswd or username or email ma trajaanich lil login trajaani lil auth w mn ghyr me tatini error messages
/**
 * @Route("/auth", name="login", methods={"POST"})
 */
public function login(Request $request, EntityManagerInterface $entityManager): Response
{
    // Get the submitted username, email and password
    $username = $request->request->get('user_name');
    $email = $request->request->get('email');
    $password = $request->request->get('password');

    // Find the user by username
    $userRepository = $entityManager->getRepository(User::class);
    $user = $userRepository->findOneBy(['userName' => $username]);

    if (!$user) {
        // User not found, handle error
        return $this->errorLogin('Invalid username.');
    }

    // Check the submitted email
    if ($email !== $user->getEmail()) {
        // Invalid email, handle error
        return $this->errorLogin('Invalid email.');
    }

    // Check the submitted password
    if (!password_verify($password, $user->getPassword())) {
        // Invalid password, handle error
        return $this->errorLogin('Invalid password.');
    }

    // Login successful, login the user and redirect to the index
    $request->getSession()->set('user', $user);


    return $this->redirectToRoute('employe_index');
}

private function errorLogin(string $errorMessage): Response
{
    // Add a flash message and redirect back to the form
    $this->addFlash('error', $errorMessage);
    return $this->redirectToRoute('login_failed');
}
/**
 * @Route("/login-faild", name="login_failed", methods={"GET"})
 */
public function loginFailed(): Response
{
    // Render the login form view
    return $this->render('auth.html.twig',[
        'registered'=>true
    ]);
}

/**
 * @Route("/logout", name="logout")
 */
public function logout(Request $request)
{
    // Clear the session
    $request->getSession()->clear();
    // Redirect to the login page
    return $this->redirectToRoute('auth_page');
}
}
?>