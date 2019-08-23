<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\NotifierApi\Api\Data\ChannelInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Channel extends AbstractDb
{
    /**
     * Notifier channels table name
     */
    private const TABLE_NAME = 'magento_notifier_channel';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            self::TABLE_NAME,
            ChannelInterface::ID
        );
    }
}
