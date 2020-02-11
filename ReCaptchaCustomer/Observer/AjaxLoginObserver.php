<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptcha\Model\CaptchaConfigInterface;
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptchaCustomer\Model\IsEnabledForCustomerLoginInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterfaceFactory;

/**
 * AjaxLoginObserver
 */
class AjaxLoginObserver implements ObserverInterface
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
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var IsEnabledForCustomerLoginInterface
     */
    private $isEnabledForCustomerLogin;

    /**
     * @var ValidationConfigInterfaceFactory
     */
    private $validationConfigFactory;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param CaptchaConfigInterface $captchaConfig
     * @param IsEnabledForCustomerLoginInterface $isEnabledForCustomerLogin
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        CaptchaConfigInterface $captchaConfig,
        IsEnabledForCustomerLoginInterface $isEnabledForCustomerLogin,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->captchaConfig = $captchaConfig;
        $this->isEnabledForCustomerLogin = $isEnabledForCustomerLogin;
        $this->validationConfigFactory = $validationConfigFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForCustomerLogin->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            $reCaptchaResponse = '';
            if ($content = $controller->getRequest()->getContent()) {
                try {
                    $jsonParams = $this->serializer->unserialize($content);
                    if (isset($jsonParams[ValidateInterface::PARAM_RECAPTCHA_RESPONSE])) {
                        $reCaptchaResponse = $jsonParams[ValidateInterface::PARAM_RECAPTCHA_RESPONSE];
                    }
                } catch (\Exception $e) {
                    $this->handleCaptchaError($controller);
                    return;
                }
            }

            /** @var ValidationConfigInterface $validationConfig */
            $validationConfig = $this->validationConfigFactory->create(
                [
                    'privateKey' => $this->captchaConfig->getPrivateKey(),
                    'captchaType' => $this->captchaConfig->getCaptchaType(),
                    'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                    'scoreThreshold' => $this->captchaConfig->getScoreThreshold(),
                ]
            );

            if (!$this->validate->validate($reCaptchaResponse, $validationConfig)) {
                $this->handleCaptchaError($controller);
            }
        }
    }

    /**
     * Handle captcha error
     *
     * @param Action $controller
     */
    private function handleCaptchaError(Action $controller)
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $jsonPayload = $this->serializer->serialize([
            'errors' => true,
            'message' => $this->captchaConfig->getErrorMessage(),
        ]);

        $controller->getResponse()->representJson($jsonPayload);
    }
}
