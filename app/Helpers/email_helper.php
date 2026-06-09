<?php

/**
 * Email Helper for Hind Bihar
 *
 * Provides a simple send_email() function that wraps the Email service.
 *
 * Usage:
 *   $sent = send_email('user@example.com', 'Subject', '<p>Message</p>');
 */

if (!function_exists('send_email')) {
    /**
     * Send an email using the configured Email service.
     *
     * @param string $to      Recipient email address
     * @param string $subject Email subject
     * @param string $message Email body (HTML or plain text)
     * @return bool True on success, false on failure
     */
    function send_email(string $to, string $subject, string $message): bool
    {
        $email = service('email');

        // Set from address from config or fall back to a default
        $emailConfig = config('Email');
        $fromEmail = $emailConfig->fromEmail ?: 'noreply@hindbihar.local';
        $fromName  = $emailConfig->fromName ?: 'Hind Bihar';

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);

        // Set mail type to HTML if the message contains HTML tags
        if (strip_tags($message) !== $message) {
            $email->setMailType('html');
        }

        if (!$email->send()) {
            log_message('error', 'Email failed to send to ' . $to . ': ' . $email->printDebugger('headers'));
            return false;
        }

        log_message('info', 'Email sent to ' . $to . ' with subject "' . $subject . '"');
        return true;
    }
}
