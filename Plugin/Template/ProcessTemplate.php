<?php

declare(strict_types=1);

namespace Web200\Mailjet\Plugin\Template;

use Magento\Email\Model\Template;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class ProcessTemplate
 *
 * @package     Web200\Mailjet\Plugin\Template
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class ProcessTemplate
{
    /**
     * @var int
     */
    protected const TYPE_HTML = 2;
    /**
     * @var Json json
     */
    protected $json;

    /**
     * ProcessTemplate constructor.
     *
     * @param Json $json
     */
    public function __construct(
        Json $json
    ) {
        $this->json = $json;
    }

    /**
     * Override process template
     *
     * @param Template $template
     * @param callable $proceed
     * @return Template
     */
    public function aroundProcessTemplate(Template $template, callable $proceed): string
    {
        if (!preg_match('/^mailjet/', $template->getData('template_id'))) {
            return $proceed();
        } else {
            $template->setTemplateType(self::TYPE_HTML);
            $body = [
                'template_id' => preg_replace('/mailjet_/', ' ', $template->getId()),
                'variables' => $this->getTemplateVariables($template)
            ];

            return $this->json->serialize($body);
        }
    }

    /**
     * Get Template Variables
     * Can't find better way to access variables templates
     *
     * @param $template
     * @return array
     */
    protected function getTemplateVariables($template): array
    {
        try {
            $rp = new \ReflectionProperty(Template::class, '_vars');
            $rp->setAccessible(true);
            $final     = [];
            $variables = $rp->getValue($template);
            foreach ($variables as $key => $value) {
                if (is_string($value)) {
                    $final[$key] = $value;
                }
                if (is_object($variables[$key])) {
                    $final = $this->getMethodValue($final, $key, $variables[$key]);
                }
            }

            return $final;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getMethodValue($final, $key, $object)
    {
        $variableClass = new \ReflectionClass(get_class($object));
        $publicMethods = $variableClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $publicMethod) {
            if (empty($publicMethod->getParameters()) && 0 === strpos($publicMethod->getName(), 'get')) {
                $objectValue = $object->{$publicMethod->getName()}();
                if (is_string($objectValue)) {
                    $final[$key . preg_replace(
                        '/get/',
                        '',
                        $this->camelCaseToUnderscore($publicMethod->getName()))] = $objectValue;
                }
            }
        }

        return $final;
    }

    /**
     * CamelCase To Underscore
     *
     * @param $input
     * @return string
     */
    protected function camelCaseToUnderscore($input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
