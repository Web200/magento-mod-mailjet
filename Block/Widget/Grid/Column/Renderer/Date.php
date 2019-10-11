<?php

declare(strict_types=1);

namespace Web200\Mailjet\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Date
 *
 * @package     Web200\Mailjet\Block\Widget\Grid\Column\Renderer
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Date extends AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param DataObject $row
     * @return  string
     * @throws \Exception
     */
    public function render(DataObject $row)
    {
        $date = $this->_getValue($row);
        if ($date) {
            if (!($date instanceof \DateTimeInterface)) {
                $date = new \DateTime($date);
            }

            return $date->format('d/m/Y');
        }

        return $this->getColumn()->getDefault();
    }
}
