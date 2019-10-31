<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Validator;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierApi\Api\AdaptersPoolInterface;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Magento\NotifierTemplateApi\Model\DatabaseTemplate\Validator\ValidateDatabaseTemplateInterface;

class ValidateAdapter implements ValidateDatabaseTemplateInterface
{
    /**
     * @var AdaptersPoolInterface
     */
    private $adapterRepository;

    /**
     * @param AdaptersPoolInterface $adapterRepository
     */
    public function __construct(
        AdaptersPoolInterface $adapterRepository
    ) {
        $this->adapterRepository = $adapterRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(DatabaseTemplateInterface $template): bool
    {
        if (!trim($template->getAdapterCode())) {
            return true;
        }

        try {
            $this->adapterRepository->getAdapterByCode($template->getAdapterCode());
        } catch (NoSuchEntityException $e) {
            throw new ValidatorException(__('Invalid adapter code'));
        }

        return true;
    }
}
