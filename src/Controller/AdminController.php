<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\AdminConfig;
use App\Entity\User;
use App\Service\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Admin Controller
 * 
 * Handles all administrative functions including user management, contact management,
 * system settings, and dashboard analytics. All routes require ROLE_ADMIN permission.
 * 
 * @package App\Controller
 */
#[Route('/admin')]
class AdminController extends AbstractController
{
    /**
     * Constructor
     * 
     * @param ContactService $contactService Service for handling contact operations
     * @param EntityManagerInterface $entityManager Doctrine entity manager
     * @param UserPasswordHasherInterface $passwordHasher Password hashing service
     */
    public function __construct(
        private ContactService $contactService,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Admin Login Page
     * 
     * Displays the admin login form. If user is already authenticated,
     * redirects to admin dashboard.
     * 
     * @param AuthenticationUtils $authenticationUtils Symfony authentication utilities
     * @return Response Rendered login page with error messages if any
     */
    #[Route('/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Admin Logout
     * 
     * Handles admin logout. This method is intercepted by the security firewall.
     * The actual logout logic is handled by Symfony's security system.
     * 
     * @return void This method should never be called directly
     * @throws \LogicException Always thrown as this method is intercepted
     */
    #[Route('/logout', name: 'admin_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Admin Dashboard
     * 
     * Displays the main admin dashboard with analytics and recent activity.
     * Shows contact statistics, API call metrics, and recent data.
     * 
     * @return Response Dashboard page with analytics data
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $contactRepo = $this->entityManager->getRepository(Contact::class);
        $recentContacts = $contactRepo->findBy([], ['createdAt' => 'DESC'], 5);
        $totalContacts = $contactRepo->count([]);

        $apiCallRepo = $this->entityManager->getRepository(\App\Entity\ApiCall::class);
        $apiCallsThisMonth = $apiCallRepo->getCallsThisMonth();
        $apiCallsToday = $apiCallRepo->getCallsToday();
        $apiCallsTotal = $apiCallRepo->getTotalCalls();
        $apiCallsSuccess = $apiCallRepo->getSuccessfulCallsThisMonth();
        $apiAvgResponse = $apiCallRepo->getAverageResponseTimeThisMonth();
        $apiTopEndpoints = $apiCallRepo->getTopEndpointsThisMonth(5);
        $apiRecentCalls = $apiCallRepo->getRecentCalls(5);

        return $this->render('admin/dashboard.html.twig', [
            'recent_contacts' => $recentContacts,
            'total_contacts' => $totalContacts,
            'api_calls_this_month' => $apiCallsThisMonth,
            'api_calls_today' => $apiCallsToday,
            'api_calls_total' => $apiCallsTotal,
            'api_calls_success' => $apiCallsSuccess,
            'api_avg_response' => $apiAvgResponse,
            'api_top_endpoints' => $apiTopEndpoints,
            'api_recent_calls' => $apiRecentCalls,
        ]);
    }

    /**
     * Contact List Page
     * 
     * Displays a paginated list of all contact form submissions.
     * Supports pagination with configurable page size.
     * 
     * @param Request $request HTTP request containing pagination parameters
     * @return Response Contact list page with pagination
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/contacts', name: 'admin_contacts')]
    public function contacts(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $contactRepo = $this->entityManager->getRepository(Contact::class);
        $contacts = $contactRepo->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
        $totalContacts = $contactRepo->count([]);
        $totalPages = ceil($totalContacts / $limit);

        return $this->render('admin/contacts.html.twig', [
            'contacts' => $contacts,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_contacts' => $totalContacts,
        ]);
    }

    /**
     * Contact Detail View
     * 
     * Displays detailed information about a specific contact submission.
     * Shows all contact form fields and submission timestamp.
     * 
     * @param Contact $contact Contact entity to display (automatically resolved by Symfony)
     * @return Response Contact detail page
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/contact/{id}', name: 'admin_contact_view')]
    public function viewContact(Contact $contact): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/contact_view.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * System Settings Page
     * 
     * Displays and handles updates to system configuration settings.
     * Supports both GET (display form) and POST (update settings) methods.
     * 
     * @param Request $request HTTP request containing form data
     * @return Response Settings page or redirect after update
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/settings', name: 'admin_settings')]
    public function settings(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $adminEmail = $request->request->get('admin_email');
            $ccEmail = $request->request->get('cc_email');
            $recaptchaSiteKey = $request->request->get('recaptcha_site_key');
            $recaptchaSecretKey = $request->request->get('recaptcha_secret_key');

            if ($adminEmail) {
                $this->contactService->setAdminConfig('admin_email', $adminEmail);
            }
            if ($ccEmail !== null) {
                $this->contactService->setAdminConfig('cc_email', $ccEmail);
            }
            if ($recaptchaSiteKey) {
                $this->contactService->setAdminConfig('recaptcha_site_key', $recaptchaSiteKey);
            }
            if ($recaptchaSecretKey) {
                $this->contactService->setAdminConfig('recaptcha_secret_key', $recaptchaSecretKey);
            }

            $this->addFlash('success', 'Settings updated successfully!');
            return $this->redirectToRoute('admin_settings');
        }

        $adminEmail = $this->contactService->getAdminConfig('admin_email') ?? '';
        $ccEmail = $this->contactService->getAdminConfig('cc_email') ?? '';
        $recaptchaSiteKey = $this->contactService->getAdminConfig('recaptcha_site_key') ?? '';
        $recaptchaSecretKey = $this->contactService->getAdminConfig('recaptcha_secret_key') ?? '';

        return $this->render('admin/settings.html.twig', [
            'admin_email' => $adminEmail,
            'cc_email' => $ccEmail,
            'recaptcha_site_key' => $recaptchaSiteKey,
            'recaptcha_secret_key' => $recaptchaSecretKey,
        ]);
    }

    /**
     * API Setup Page
     * 
     * Displays API documentation and setup instructions for developers.
     * Shows JWT authentication details and endpoint information.
     * 
     * @return Response API setup documentation page
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/api-setup', name: 'admin_api_setup')]
    public function apiSetup(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/api_setup.html.twig');
    }

    /**
     * User List Page
     * 
     * Displays a paginated list of all system users.
     * Supports pagination and shows user status and roles.
     * 
     * @param Request $request HTTP request containing pagination parameters
     * @return Response User list page with pagination
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/users', name: 'admin_users')]
    public function users(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $userRepo = $this->entityManager->getRepository(User::class);
        $users = $userRepo->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
        $totalUsers = $userRepo->count([]);
        $totalPages = ceil($totalUsers / $limit);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_users' => $totalUsers,
        ]);
    }

    /**
     * Create User Page
     * 
     * Displays form to create a new user and handles user creation.
     * Supports both GET (display form) and POST (create user) methods.
     * Validates required fields and handles password hashing.
     * 
     * @param Request $request HTTP request containing user data
     * @return Response User creation form or redirect after creation
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/user/create', name: 'admin_user_create')]
    public function createUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $firstName = $request->request->get('first_name');
            $lastName = $request->request->get('last_name');
            $password = $request->request->get('password');
            $roles = $request->request->all('roles') ?? [];
            $isActive = $request->request->getBoolean('is_active', true);

            // Validate required fields
            if (!$email || !$firstName || !$lastName || !$password) {
                $this->addFlash('error', 'All fields are required.');
                return $this->render('admin/user_form.html.twig', [
                    'user' => null,
                    'available_roles' => $this->getAvailableRoles(),
                ]);
            }

            // Check if email already exists
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'A user with this email already exists.');
                return $this->render('admin/user_form.html.twig', [
                    'user' => null,
                    'available_roles' => $this->getAvailableRoles(),
                ]);
            }

            $user = new User();
            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setRoles($roles);
            $user->setIsActive($isActive);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'User created successfully!');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'user' => null,
            'available_roles' => $this->getAvailableRoles(),
        ]);
    }

    /**
     * Edit User Page
     * 
     * Displays form to edit an existing user and handles user updates.
     * Supports both GET (display form) and POST (update user) methods.
     * Validates required fields and handles optional password updates.
     * 
     * @param User $user User entity to edit (automatically resolved by Symfony)
     * @param Request $request HTTP request containing user data
     * @return Response User edit form or redirect after update
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/user/{id}/edit', name: 'admin_user_edit')]
    public function editUser(User $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $firstName = $request->request->get('first_name');
            $lastName = $request->request->get('last_name');
            $password = $request->request->get('password');
            $roles = $request->request->all('roles') ?? [];
            $isActive = $request->request->getBoolean('is_active', true);

            // Validate required fields
            if (!$email || !$firstName || !$lastName) {
                $this->addFlash('error', 'Email, first name, and last name are required.');
                return $this->render('admin/user_form.html.twig', [
                    'user' => $user,
                    'available_roles' => $this->getAvailableRoles(),
                ]);
            }

            // Check if email already exists (excluding current user)
            $existingUser = $this->entityManager->getRepository(User::class)
                ->createQueryBuilder('u')
                ->where('u.email = :email AND u.id != :id')
                ->setParameter('email', $email)
                ->setParameter('id', $user->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingUser) {
                $this->addFlash('error', 'A user with this email already exists.');
                return $this->render('admin/user_form.html.twig', [
                    'user' => $user,
                    'available_roles' => $this->getAvailableRoles(),
                ]);
            }

            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setRoles($roles);
            $user->setIsActive($isActive);
            $user->setUpdatedAt();

            // Only update password if provided
            if ($password) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'User updated successfully!');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'user' => $user,
            'available_roles' => $this->getAvailableRoles(),
        ]);
    }

    /**
     * Delete User
     * 
     * Deletes a user from the system. Users cannot delete themselves.
     * Requires POST method for security. Shows error if attempting to delete self.
     * 
     * @param User $user User entity to delete (automatically resolved by Symfony)
     * @return Response Redirect to user list with success/error message
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Prevent self-deletion
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'You cannot delete your own account.');
            return $this->redirectToRoute('admin_users');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('admin_users');
    }

    /**
     * Toggle User Status
     * 
     * Activates or deactivates a user account. Users cannot deactivate themselves.
     * Requires POST method for security. Shows error if attempting to deactivate self.
     * 
     * @param User $user User entity to toggle status (automatically resolved by Symfony)
     * @return Response Redirect to user list with success/error message
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException If user lacks ROLE_ADMIN
     */
    #[Route('/user/{id}/toggle-status', name: 'admin_user_toggle_status', methods: ['POST'])]
    public function toggleUserStatus(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Prevent self-deactivation
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'You cannot deactivate your own account.');
            return $this->redirectToRoute('admin_users');
        }

        $user->setIsActive(!$user->isActive());
        $user->setUpdatedAt();
        $this->entityManager->flush();

        $status = $user->isActive() ? 'activated' : 'deactivated';
        $this->addFlash('success', "User {$status} successfully!");
        return $this->redirectToRoute('admin_users');
    }

    /**
     * Get Available Roles
     * 
     * Returns an array of all available user roles in the system.
     * Used by user forms to populate role selection dropdowns.
     * 
     * @return array Array of available role names
     */
    private function getAvailableRoles(): array
    {
        return [
            'ROLE_USER' => 'User',
            'ROLE_API_USER' => 'API User',
            'ROLE_ADMIN' => 'Administrator',
        ];
    }
} 