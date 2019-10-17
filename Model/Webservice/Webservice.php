<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model\Webservice;

use Web200\Mailjet\Model\Config;
use Web200\Mailjet\Logger\Logger;
use \Mailjet\Resources;
use \Mailjet\Client as MailjetClient;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Webservice
 *
 * @package   Web200\Mailjet\Model\Webservice
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Webservice
{
    /**
     * @var string
     */
    protected $apiVersion = 'v3.1';
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Webservice constructor.
     *
     * @param Logger  $logger
     * @param Config  $config
     */
    public function __construct(
        Logger $logger,
        Config $config
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Init Api
     *
     * @return MailjetClient
     * @throws LocalizedException
     */
    protected function initApi(): MailjetClient
    {
        $this->checkLogin();

        $api = new MailjetClient(
            $this->config->getApiKeyPublic(),
            $this->config->getApiKeyPrivate(),
            true,
            ['version' => $this->apiVersion]
        );
        return $api;
    }

    /**
     * Check login information
     *
     * @return bool
     * @throws LocalizedException
     */
    protected function checkLogin(): bool
    {
        if ($this->config->getApiKeyPublic() === '') {
            throw new LocalizedException(__('Api Public Key empty'));
        }
        if ($this->config->getApiKeyPrivate() === '') {
            throw new LocalizedException(__('Api Secret Key empty'));
        }
        return true;
    }
}
