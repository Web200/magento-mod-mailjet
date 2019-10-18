<?php

declare(strict_types=1);

namespace Web200\Mailjet\Plugin\Template;

use Web200\Mailjet\Helper\Config as MailjetConfig;
use Web200\Mailjet\Model\Config\Source\Template as MailjetTemplate;

/**
 * Class Source
 *
 * @package     Web200\Mailjet\Plugin\Template
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Source
{
    /**
     * @var MailjetConfig
     */
    protected $mailjetConfig;
    /**
     * @var MailjetTemplate
     */
    protected $mailjetTemplate;

    /**
     * SourceEmailTemplate constructor.
     *
     * @param MailjetConfig   $mailjetConfig
     * @param MailjetTemplate $mailjetTemplate
     */
    public function __construct(
        MailjetConfig $mailjetConfig,
        MailjetTemplate $mailjetTemplate
    ) {
        $this->mailjetConfig   = $mailjetConfig;
        $this->mailjetTemplate = $mailjetTemplate;
    }

    /**
     * Add Mailjet Templates
     *
     * @param $emailTemplate
     * @param $templates
     * @return mixed
     */
    public function afterToOptionArray($emailTemplate, $templates): array
    {
        if ($this->mailjetConfig->active()) {
            $templates = $this->getMailjetTemplates($templates);
        }

        return $templates;
    }

    /**
     * Get Mailjet Templates
     *
     * @param array $templates
     * @return array
     */
    protected function getMailjetTemplates($templates): array
    {
        $mailjetTemplates = $this->mailjetTemplate->toOptionArray();
        foreach ($mailjetTemplates as $mailjetTemplate) {
            $templates[] = [
                'value' => 'mailjet_' . $mailjetTemplate['value'],
                'label' => '[Mailjet] ' . $mailjetTemplate['label'],
            ];
        }

        return $templates;
    }
}
