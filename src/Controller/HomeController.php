<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Home Controller
 * 
 * Handles public-facing pages including the homepage, contact form,
 * about page, and API documentation. These routes are publicly accessible.
 * 
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * Constructor
     * 
     * @param ContactService $contactService Service for handling contact operations
     */
    public function __construct(
        private ContactService $contactService
    ) {
    }

    /**
     * Homepage
     * 
     * Displays the main landing page with feature overview, technology stack,
     * and call-to-action buttons. This is the primary entry point for visitors.
     * 
     * @return Response Rendered homepage with feature showcase
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * Contact Form Page
     * 
     * Displays and handles the contact form submission. Includes reCAPTCHA validation
     * and email notifications. Supports both GET (display form) and POST (process form) methods.
     * 
     * Features:
     * - Form validation with Symfony Form component
     * - reCAPTCHA integration for spam protection
     * - Database storage of contact submissions
     * - Email notifications to administrators
     * - Error handling and user feedback
     * 
     * @param Request $request HTTP request containing form data and reCAPTCHA response
     * @return Response Contact form page or redirect after successful submission
     */
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validate reCAPTCHA
            $recaptchaResponse = $request->request->get('g-recaptcha-response');
            if (!$this->contactService->validateRecaptcha($recaptchaResponse)) {
                $this->addFlash('error', 'reCAPTCHA validation failed. Please try again.');
                $recaptchaSiteKey = $this->contactService->getAdminConfig('recaptcha_site_key');
                return $this->render('contact/index.html.twig', [
                    'form' => $form->createView(),
                    'recaptcha_site_key' => $recaptchaSiteKey,
                ]);
            }

            try {
                // Save contact
                $this->contactService->saveContact($contact);
                
                // Send emails
                $this->contactService->sendContactEmails($contact);
                
                $this->addFlash('success', 'Thank you for your message! We will get back to you soon.');
                return $this->redirectToRoute('app_contact');
            } catch (\Exception $e) {
                $this->addFlash('error', 'There was an error sending your message. Please try again.');
            }
        }

        $recaptchaSiteKey = $this->contactService->getAdminConfig('recaptcha_site_key');
        
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'recaptcha_site_key' => $recaptchaSiteKey,
        ]);
    }

    /**
     * About Page
     * 
     * Displays information about the application, its features, and the development team.
     * Static content page with company/project information.
     * 
     * @return Response Rendered about page
     */
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    /**
     * API Documentation Page
     * 
     * Displays API documentation and usage instructions for developers.
     * Shows available endpoints, authentication methods, and example requests.
     * 
     * @return Response Rendered API documentation page
     */
    #[Route('/api-docs', name: 'app_api_docs')]
    public function apiDocs(): Response
    {
        return $this->render('home/api-docs.html.twig');
    }
} 