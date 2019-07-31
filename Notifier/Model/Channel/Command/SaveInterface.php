<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model\Channel\Command;

use Magento\Framework\Exception\CouldNotSaveException;
use MSP\NotifierApi\Api\Data\ChannelInterface;

/**
 * Save Channel data command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Save call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \MSP\NotifierApi\Api\ChannelRepositoryInterface
 * @api
 */
interface SaveInterface
{
    /**
     * Save Channel data
     *
     * @param ChannelInterface $source
     * @return int
     * @throws CouldNotSaveException
     */
    public function execute(ChannelInterface $source): int;
}
