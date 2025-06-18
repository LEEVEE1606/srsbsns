<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private ContactService $contactService
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

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

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/api-docs', name: 'app_api_docs')]
    public function apiDocs(): Response
    {
        return $this->render('home/api-docs.html.twig');
    }
} 