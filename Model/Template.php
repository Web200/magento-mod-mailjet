<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model;

use \Mailjet\Resources;
use Web200\Mailjet\Logger\Logger;

/**
 * Class Template
 *
 * @category    Class
 * @package     Web200\Mailjet\Model
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Template
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Template constructor.
     *
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        Config $config,
        Logger $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getTemplates()
    {
        $templates = [];
        try {
            $api = new \Mailjet\Client(
                $this->config->getApiKeyPublic(),
                $this->config->getApiKeyPrivate()
            );
            $filters = [
                'OwnerType' => 'user',
                'Limit' => '100'
            ];
            $response = $api->get(Resources::$Template, ['filters' => $filters]);
            if (!$response->success()) {
                $this->logger->error($response->getReasonPhrase());
            }

            $rows = $response->getBody()['Data'];
            foreach ($rows as $row) {
                $templates[$row['ID']] =  $row['Name'];
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $templates;
    }
}
