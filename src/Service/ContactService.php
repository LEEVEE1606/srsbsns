<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\AdminConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContactService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    ) {
    }

    public function saveContact(Contact $contact): void
    {
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
    }

    public function sendContactEmails(Contact $contact): void
    {
        // Send confirmation email to user
        $this->sendUserConfirmationEmail($contact);
        
        // Send notification email to admin
        $this->sendAdminNotificationEmail($contact);
    }

    private function sendUserConfirmationEmail(Contact $contact): void
    {
        $email = (new Email())
            ->from('noreply@srsbsns.local')
            ->to($contact->getEmail())
            ->subject('Thank you for contacting us')
            ->html($this->getUserConfirmationEmailTemplate($contact));

        $this->mailer->send($email);
    }

    private function sendAdminNotificationEmail(Contact $contact): void
    {
        $adminEmail = $this->getAdminEmail();
        $ccEmail = $this->getAdminConfig('cc_email');
        
        $email = (new Email())
            ->from('noreply@srsbsns.local')
            ->to($adminEmail)
            ->subject('New Contact Form Submission')
            ->html($this->getAdminNotificationEmailTemplate($contact));

        // Add CC if configured
        if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
            $email->cc($ccEmail);
        }

        $this->mailer->send($email);
    }

    private function getUserConfirmationEmailTemplate(Contact $contact): string
    {
        return "
            <h2>Thank you for contacting us!</h2>
            <p>Dear {$contact->getName()},</p>
            <p>We have received your message and will get back to you as soon as possible.</p>
            <p><strong>Your message:</strong></p>
            <p>{$contact->getMessage()}</p>
            <br>
            <p>Best regards,<br>SRSBSNS Test App Team</p>
        ";
    }

    private function getAdminNotificationEmailTemplate(Contact $contact): string
    {
        return "
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> {$contact->getName()}</p>
            <p><strong>Email:</strong> {$contact->getEmail()}</p>
            <p><strong>Telephone:</strong> {$contact->getTelephone()}</p>
            <p><strong>Message:</strong></p>
            <p>{$contact->getMessage()}</p>
            <p><strong>Submitted:</strong> {$contact->getCreatedAt()->format('Y-m-d H:i:s')}</p>
        ";
    }

    private function getAdminEmail(): string
    {
        $configRepo = $this->entityManager->getRepository(AdminConfig::class);
        $config = $configRepo->findOneBy(['configKey' => 'admin_email']);
        
        return $config ? $config->getConfigValue() : 'admin@srsbsns.local';
    }

    public function getAdminConfig(string $key): ?string
    {
        $configRepo = $this->entityManager->getRepository(AdminConfig::class);
        $config = $configRepo->findOneBy(['configKey' => $key]);
        
        return $config ? $config->getConfigValue() : null;
    }

    public function setAdminConfig(string $key, string $value): void
    {
        $configRepo = $this->entityManager->getRepository(AdminConfig::class);
        $config = $configRepo->findOneBy(['configKey' => $key]);
        
        if (!$config) {
            $config = new AdminConfig();
            $config->setConfigKey($key);
        }
        
        $config->setConfigValue($value);
        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }

    public function validateRecaptcha(string $recaptchaResponse): bool
    {
        $recaptchaSecret = $this->getAdminConfig('recaptcha_secret_key');
        
        if (empty($recaptchaSecret) || empty($recaptchaResponse)) {
            return false;
        }

        $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret);
        $resp = $recaptcha->verify($recaptchaResponse);
        
        return $resp->isSuccess();
    }
} 