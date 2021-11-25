<?php

namespace App\Application\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Service to send emails
 */
class EmailService
{

    private $logService;

    private $mailer;

    private $settingService;

    public function __construct(LogService $logService, MailerInterface $mailer, SettingService $settingService)
    {
        $this->logService = $logService;
        $this->mailer = $mailer;
        $this->settingService = $settingService;
    }

    /**
     * @param string $template
     * @param array|string $to
     * @param string $subject
     * @param array $templateVariables
     */
    public function send(string $template, array $to, string $subject, array $templateVariables = []): void
    {
        $toAddress = is_array($to) ? new Address($to['address'], $to['name']) : new Address($to);

        $email = (new TemplatedEmail())
            ->to($toAddress)
//            ->from(new Address(
//                $this->settingService->get('application.from-email', 'noreply@woutercarabain.com'),
//                $this->settingService->get('application.from-name', 'Fit U App')
//            ))
            ->subject($subject)
            ->context($templateVariables)
            ->htmlTemplate(sprintf('email/%s.html.twig', $template))
            ->textTemplate(sprintf('email/%s.text.twig', $template));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logService->warning(
                sprintf('Cannot send email message: %s', $e->getMessage()),
                [
                    'exception' => $e,
                    'template' => $template,
                    'to' => $to,
                    'subject' => $subject,
                    'templateVariables' => $templateVariables,
                ]
            );
        }
    }

    /**
     * @param string $template
     * @param User $user
     * @param string $subject
     * @param array $templateVariables
     */
    public function sendToUser(string $template, User $user, string $subject, array $templateVariables = []): void
    {
        $this->send(
            $template,
            ['address' => $user->getEmail(), 'name' => $user->getUsername()],
            $subject,
            $templateVariables
        );
    }
}