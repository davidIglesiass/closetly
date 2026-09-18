<?php

class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $host = getenv('MAIL_HOST') ?: 'mailpit';
        $port = (int) (getenv('MAIL_PORT') ?: 1025);
        $from = getenv('MAIL_FROM') ?: 'noreply@closetly.test';

        // Strip header-injection characters from attacker-reachable values.
        $to = str_replace(["\r", "\n"], '', $to);
        $subject = str_replace(["\r", "\n"], '', $subject);

        $socket = @fsockopen($host, $port, $errno, $errstr, 3);
        if (!$socket) {
            error_log("Mailer: could not connect to {$host}:{$port} - {$errstr}");
            return false;
        }

        try {
            self::expect($socket, 220);
            self::command($socket, "HELO closetly\r\n", 250);
            self::command($socket, "MAIL FROM:<{$from}>\r\n", 250);
            self::command($socket, "RCPT TO:<{$to}>\r\n", 250);
            self::command($socket, "DATA\r\n", 354);

            $headers = "From: Closetly <{$from}>\r\nTo: <{$to}>\r\nSubject: {$subject}\r\n"
                . "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
            // Dot-stuffing per RFC 5321 - a lone "." would otherwise end the DATA block early.
            $escapedBody = preg_replace('/^\./m', '..', $body);
            fputs($socket, $headers . "\r\n" . $escapedBody . "\r\n.\r\n");
            self::expect($socket, 250);

            fputs($socket, "QUIT\r\n");
            return true;
        } catch (Exception $e) {
            error_log('Mailer: ' . $e->getMessage());
            return false;
        } finally {
            fclose($socket);
        }
    }

    private static function command($socket, string $line, int $expectedCode): void
    {
        fputs($socket, $line);
        self::expect($socket, $expectedCode);
    }

    private static function expect($socket, int $expectedCode): void
    {
        $response = fgets($socket, 512);
        if ($response === false || (int) substr($response, 0, 3) !== $expectedCode) {
            throw new Exception('Unexpected SMTP response: ' . trim((string) $response));
        }
    }
}
