<?php

declare(strict_types=1);

namespace Web200\Mailjet\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class Handler
 *
 * @package   Web200\Mailjet\Logger
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Handler extends Base
{
    protected $fileName = '/var/log/mailjet.log';
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;
}
