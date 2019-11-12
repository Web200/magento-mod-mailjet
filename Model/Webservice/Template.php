<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model\Webservice;

use \Mailjet\Resources;
use Web200\Mailjet\Helper\Config;

/**
 * Class Template
 *
 * @package   Web200\Mailjet\Model\Webservice
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Template extends Webservice
{
    /**
     * @var string
     */
    protected $apiVersion = 'v3';
    /**
     * @var array
     */
    protected $cacheTemplates;
    /**
     * @var int
     */
    protected $limit = 100;
    /**
     * @var string
     */
    protected $ownerType = 'user';

    /**
     * Get Limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Set Limit
     *
     * @param int $limit
     * @return Template
     */
    public function setLimit(int $limit): Template
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get Owner Type
     *
     * @return string
     */
    public function getOwnerType(): string
    {
        return $this->ownerType;
    }

    /**
     * Set Owner Type
     *
     * @param string $ownerType
     * @return Template
     */
    public function setOwnerType(string $ownerType): Template
    {
        $this->ownerType = $ownerType;
        return $this;
    }

    /**
     * Get Mailjet Templates
     *
     * @return array
     */
    public function getTemplates(): array
    {
        if ($this->cacheTemplates === null) {
            $this->cacheTemplates = [];
            try {
                $api      = $this->initApi(Config::KIND_TRANSACTIONAL);
                $filters  = [
                    'OwnerType' => $this->getOwnerType(),
                    'Limit' => $this->getLimit()
                ];
                $response = $api->get(Resources::$Template, ['filters' => $filters]);
                if (!$response->success()) {
                    $this->logger->error($response->getReasonPhrase());
                }
                $rows = $response->getBody()['Data'];
                foreach ($rows as $row) {
                    $this->cacheTemplates[$row['ID']] = $row['Name'];
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        return $this->cacheTemplates;
    }
}
