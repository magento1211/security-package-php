<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model;

use Magento\Framework\Filter\Template;
use MSP\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;
use MSP\NotifierTemplateApi\Model\GetMessageTextInterface;
use MSP\NotifierTemplateApi\Model\VariablesDecorator\DecorateVariablesInterface;
use Psr\Log\LoggerInterface;

class GetMessageText implements GetMessageTextInterface
{
    /**
     * @var DecorateVariablesInterface
     */
    private $decorateVariables;

    /**
     * @var TemplateGetterInterface
     */
    private $templateGetter;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Template $template
     * @param TemplateGetterInterface $templateGetter
     * @param DecorateVariablesInterface $decorateVariables
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        Template $template,
        TemplateGetterInterface $templateGetter,
        DecorateVariablesInterface $decorateVariables,
        LoggerInterface $logger
    ) {
        $this->decorateVariables = $decorateVariables;
        $this->templateGetter = $templateGetter;
        $this->template = $template;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $channelCode, string $templateId, array $params = []): string
    {
        $template = $this->templateGetter->getTemplate($channelCode, $templateId);
        if (!$template) {
            return '';
        }

        $this->decorateVariables->execute($params);

        $this->template->setVariables($params);
        try {
            return $this->template->filter($template);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return '';
        }
    }
}
