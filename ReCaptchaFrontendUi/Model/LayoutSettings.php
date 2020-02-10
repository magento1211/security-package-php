<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\ReCaptcha\Model\ConfigInterface as ReCaptchaConfig;
use Magento\ReCaptchaFrontendUi\Model\FrontendConfigInterface as ReCaptchaFrontendUiConfig;

/**
 * Extension point of the layout configuration setting for reCaptcha
 *
 * @api
 */
class LayoutSettings
{
    /**
     * @var ReCaptchaConfig
     */
    private $reCaptchaConfig;

    /**
     * @var ReCaptchaFrontendUiConfig
     */
    private $reCaptchaFrontendConfig;

    /**
     * @var ConfigEnabledInterface[]
     */
    private $configEnabledProviders;

    /**
     * @param ReCaptchaConfig $reCaptchaConfig
     * @param ReCaptchaFrontendUiConfig $reCaptchaFrontendConfig
     * @param ConfigEnabledInterface[] $configEnabledProviders
     */
    public function __construct(
        ReCaptchaConfig $reCaptchaConfig,
        ReCaptchaFrontendUiConfig $reCaptchaFrontendConfig,
        array $configEnabledProviders
    ) {
        $this->reCaptchaConfig = $reCaptchaConfig;
        $this->reCaptchaFrontendConfig = $reCaptchaFrontendConfig;
        $this->configEnabledProviders = $configEnabledProviders;
    }

    /**
     * Return captcha config for frontend
     * @return array
     */
    public function getCaptchaSettings(): array
    {
        $settings = [
            'siteKey' => $this->reCaptchaConfig->getPublicKey(),
            'size' => $this->reCaptchaFrontendConfig->getSize(),
            'badge' => $this->reCaptchaFrontendConfig->getPosition(),
            'theme' => $this->reCaptchaFrontendConfig->getTheme(),
            'lang' => $this->reCaptchaFrontendConfig->getLanguageCode(),
        ];
        foreach ($this->configEnabledProviders as $key => $configEnabledProvider) {
            $settings['enabled'][$key] = $configEnabledProvider->isEnabled();
        }
        return $settings;
    }
}
