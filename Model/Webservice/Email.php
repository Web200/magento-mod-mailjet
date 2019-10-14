<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model\Webservice;

use \Mailjet\Resources;

/**
 * Class Email
 *
 * @package   Web200\Mailjet\Model\Webservice
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Email extends Webservice
{
    /**
     * @var string
     */
    protected $fromEmail = '';
    /**
     * @var string
     */
    protected $fromName = '';
    /**
     * array [['Email' => $email, 'Name' => $name] ]
     *
     * @var array
     */
    protected $to = '';
    /**
     * @var string
     */
    protected $replyToEmail = '';
    /**
     * @var string
     */
    protected $replyToName = '';
    /**
     * @var int
     */
    protected $templateId = null;
    /**
     * array [['var1' => $var1, 'var2' => $var2] ]
     *
     * @var array
     */
    protected $variables;

    /**
     * Send Mail
     *
     * @return bool
     */
    public function send()
    {
        try {
            $api  = $this->initApi();
            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $this->getFromEmail(),
                            'Name' => $this->getFromName()
                        ],
                        'To' => $this->getTo()
                    ]
                ]
            ];

            if ($this->getTemplateId()) {
                $body['TemplateID']       = $this->getTemplateId();
                $body['TemplateLanguage'] = true;
            }

            if ($this->getVariables() && count($this->getVariables()) > 0) {
                $body['Variables'] = $this->getVariables();
            }

            if ($this->getReplyToEmail()) {
                $body['ReplyTo'] = [
                    'Email' => $this->getReplyToEmail(),
                    'Name' => $this->getReplyToName()
                ];
            }

            $response = $api->post(Resources::$Email, ['body' => $body]);
            if (!$response->success()) {
                $this->logger->error($response->getReasonPhrase());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }
    }

    /**
     * Get From Email
     *
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * Set From Email
     *
     * @param string $fromEmail
     * @return Email
     */
    public function setFromEmail(string $fromEmail): Email
    {
        $this->fromEmail = trim($fromEmail);

        return $this;
    }

    /**
     * Get From Name
     *
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * Set From Name
     *
     * @param string $fromName
     * @return Email
     */
    public function setFromName(string $fromName): Email
    {
        $this->fromName = trim($fromName);

        return $this;
    }

    /**
     * Get To Email
     *
     * @return array
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * Set To Email
     *
     * @param array $to
     * @return Email
     */
    public function setTo($to): Email
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get Template Id
     *
     * @return int
     */
    public function getTemplateId(): ?int
    {
        return $this->templateId;
    }

    /**
     * Set Template Id
     *
     * @param int $templateId
     * @return Email
     */
    public function setTemplateId(int $templateId): Email
    {
        $this->templateId = $templateId;

        return $this;
    }

    /**
     * @return array
     */
    public function getVariables(): ?array
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     * @return Email
     */
    public function setVariables(array $variables): Email
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * @return string
     */
    public function getReplyToEmail(): string
    {
        return $this->replyToEmail;
    }

    /**
     * @param string $replyToEmail
     * @return Email
     */
    public function setReplyToEmail(string $replyToEmail): Email
    {
        $this->replyToEmail = trim($replyToEmail);

        return $this;
    }

    /**
     * @return string
     */
    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    /**
     * @param string $replyToName
     * @return Email
     */
    public function setReplyToName(string $replyToName): Email
    {
        $this->replyToName = trim($replyToName);

        return $this;
    }
}
