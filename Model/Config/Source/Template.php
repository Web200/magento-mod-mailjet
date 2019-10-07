<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Web200\Mailjet\Model\Webservice\Template as WebserviceTemplate;

/**
 * Class Template
 *
 * @category    Class
 * @package     Magento\Config\Model\Config\Source
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Template implements ArrayInterface
{
    /**
     * @var WebserviceTemplate
     */
    protected $template;

    /**
     * Template constructor.
     *
     * @param WebserviceTemplate $template
     */
    public function __construct(
        WebserviceTemplate $template
    ) {
        $this->template = $template;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $final = [];
        $templates = $this->toArray();
        foreach ($templates as $id => $name) {
            $final[] = [
                'value' => $id,
                'label' => $name,
            ];
        }
        return $final;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->template->getTemplates();
    }
}
