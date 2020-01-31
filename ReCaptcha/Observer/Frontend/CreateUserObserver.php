<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Area;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\CaptchaRequestHandler;
use Magento\ReCaptcha\Model\Config;

/**
 * CreateUserObserver
 */
class CreateUserObserver implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CaptchaRequestHandler
     */
    private $captchaRequestHandler;

    /**
     * @param UrlInterface $url
     * @param Config $config
     * @param CaptchaRequestHandler $captchaRequestHandler
     */
    public function __construct(
        UrlInterface $url,
        Config $config,
        CaptchaRequestHandler $captchaRequestHandler
    ) {
        $this->url = $url;
        $this->config = $config;
        $this->captchaRequestHandler = $captchaRequestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isAreaEnabled(Area::AREA_FRONTEND) && $this->config->isEnabledFrontendCreateUser()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/create', ['_secure' => true]);

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
