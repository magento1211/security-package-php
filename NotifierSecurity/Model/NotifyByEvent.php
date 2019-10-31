<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierSecurity\Model;

use Psr\Log\LoggerInterface;

class NotifyByEvent implements NotifierInterface
{
    /**
     * @var NotifierInterface[]
     */
    private $notifierByEvent;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @param NotifierInterface[] $notifierByEvent
     */
    public function __construct(
        LoggerInterface $logger,
        array $notifierByEvent
    ) {
        $this->notifierByEvent = $notifierByEvent;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $eventName, array $eventData): void
    {
        try {
            if (isset($this->notifierByEvent[$eventName])) {
                $this->notifierByEvent[$eventName]->execute($eventName, $eventData);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
