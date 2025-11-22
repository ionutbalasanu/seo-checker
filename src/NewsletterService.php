<?php
declare(strict_types=1);

final class NewsletterService
{
    private WordpressClient $wp;
    private string $listName;

    public function __construct(WordpressClient $wp, string $listName)
    {
        $this->wp = $wp;
        $this->listName = $listName;
    }

    /**
     * Înscrie în listă DOAR dacă ambele consimțuri sunt true.
     * Returnează un payload diagnostic pentru front-end/loguri.
     */
    public function subscribeIfConsented(
        string $email,
        ?string $firstName,
        bool $consentNewsletter,
        bool $consentTerms,
        ?string $ip = null
    ): array {
        if (!$consentNewsletter || !$consentTerms) {
            return [
                'skipped' => true,
                'reason'  => 'missing_consents',
                'ok'      => false
            ];
        }

        $resp = $this->wp->subscribe($email, $firstName, $this->listName, $ip);
        return [
            'skipped'   => false,
            'ok'        => (bool)$resp['ok'],
            'status'    => (int)$resp['status'],
            'raw'       => (string)$resp['body'],
            'json'      => $resp['json'],
        ];
    }
}
