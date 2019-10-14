<?php

declare(strict_types=1);

namespace Web200\Mailjet\Plugin;

use Magento\Framework\Mail\TransportInterface;
use Web200\Mailjet\Model\Config;
use Web200\Mailjet\Model\Mail\Api;

/**
 * Class Send
 *
 * @package     Web200\Mailjet\Plugin
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Send
{
    /**
     * Description config field
     *
     * @var Config config
     */
    protected $config;
    /**
     * Description api field
     *
     * @var Api api
     */
    protected $api;

    /**
     * MailTransport constructor.
     *
     * @param Config $config
     * @param Api    $api
     */
    public function __construct(
        Config $config,
        Api $api
    ) {
        $this->config = $config;
        $this->api    = $api;
    }

    /**
     * Override Send Message
     *
     * @param TransportInterface $transport
     * @param callable           $proceed
     * @return
     */
    public function aroundSendMessage(TransportInterface $transport, callable $proceed)
    {
        if ($this->config->active()) {
            $this->api->sendMail($transport);
        } else {
            return $proceed();
        }
    }
}
