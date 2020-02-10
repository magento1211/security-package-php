<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Recaptcha theme options
 */
class Theme implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'light', 'label' => __('Light Theme')],
            ['value' => 'dark', 'label' => __('Dark Theme')],
        ];
    }
}
