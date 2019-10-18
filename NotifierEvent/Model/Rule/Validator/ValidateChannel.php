<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Validator;

use InvalidArgumentException;
use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Magento\NotifierEventApi\Model\Rule\Validator\ValidateRuleInterface;

class ValidateChannel implements ValidateRuleInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function execute(RuleInterface $rule): bool
    {
        try {
            if (!trim($rule->getChannelsCodes()) || empty($this->serializer->unserialize($rule->getChannelsCodes()))) {
                throw new ValidatorException(__('You must specify at least one channel'));
            }
        } catch (InvalidArgumentException $e) {
            throw new ValidatorException(__('Invalid channels data format'));
        }

        return true;
    }
}
