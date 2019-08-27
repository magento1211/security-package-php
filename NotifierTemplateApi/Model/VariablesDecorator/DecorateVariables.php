<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplateApi\Model\VariablesDecorator;

class DecorateVariables implements DecorateVariablesInterface
{
    /**
     * @var DecorateVariablesInterface[]
     */
    private $decorators;

    /**
     * @param array $decorators
     * @SuppressWarnings(PHPMD.LongVariables)
     */
    public function __construct(
        array $decorators
    ) {
        $this->decorators = $decorators;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $data): array
    {
        foreach ($this->decorators as $decorator) {
            $data = $decorator->execute($data);
        }

        return $data;
    }
}
