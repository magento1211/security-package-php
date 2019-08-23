<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Magento\NotifierTemplateApi\Model\DatabaseTemplate\Validator\ValidateDatabaseTemplateInterface;

class ValidateCode implements ValidateDatabaseTemplateInterface
{
    /**
     * @inheritDoc
     */
    public function execute(DatabaseTemplateInterface $template): bool
    {
        if (!trim($template->getCode())) {
            throw new ValidatorException(__('Template identifier is required'));
        }

        if (!preg_match('/^(\w+:)?[\w_]+$/', $template->getCode())) {
            throw new ValidatorException(__('Invalid template identifier: Only alphanumeric chars + columns')
            );
        }

        return true;
    }
}
