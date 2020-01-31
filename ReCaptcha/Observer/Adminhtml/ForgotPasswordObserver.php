<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Adminhtml;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Area;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\CaptchaFailureHandling;
use Magento\ReCaptcha\Model\Config;
use Magento\ReCaptcha\Model\ValidateInterface;

/**
 * ForgotPasswordObserver
 */
class ForgotPasswordObserver implements ObserverInterface
{
    /**
     * @var ValidateInterface
     */
    private $validate;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var CaptchaFailureHandling
     */
    private $captchaFailureHandling;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param Config $config
     * @param UrlInterface $url
     * @param CaptchaFailureHandling $captchaFailureHandling
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        Config $config,
        UrlInterface $url,
        CaptchaFailureHandling $captchaFailureHandling
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
        $this->url = $url;
        $this->captchaFailureHandling = $captchaFailureHandling;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Action $controller */
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();

        if ($this->config->isAreaEnabled(Area::AREA_ADMINHTML) && null !== $request->getParam('email')) {
            $reCaptchaResponse = $request->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);
            $remoteIp = $this->remoteAddress->getRemoteAddress();

            if (!$this->validate->validate($reCaptchaResponse, $remoteIp)) {
                $url = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);
                $this->captchaFailureHandling->execute($controller->getResponse(), $url);
            }
        }
    }
}
