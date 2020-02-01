<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Captcha request handler
 */
class CaptchaRequestHandler implements CaptchaRequestHandlerInterface
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
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param Config $config
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        Config $config
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function execute(
        string $area,
        RequestInterface $request,
        HttpInterface $response,
        string $redirectOnFailureUrl
    ): void {
        $reCaptchaResponse = $request->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);
        $remoteIp = $this->remoteAddress->getRemoteAddress();
        $options['threshold'] = ($area === Area::AREA_ADMINHTML)
            ? $this->config->getMinBackendScore() : $this->config->getMinFrontendScore();

        if (false === $this->validate->validate($reCaptchaResponse, $remoteIp, $options)) {
            $this->messageManager->addErrorMessage($this->config->getErrorDescription());
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

            $response->setRedirect($redirectOnFailureUrl);
        }
    }
}
