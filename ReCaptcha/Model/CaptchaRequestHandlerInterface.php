<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Captcha request handler interface (sugar service for avoiding boilerplate code)
 *
 * Validate captcha data in request and set message and redirect if validation was failed
 *
 * @api
 */
interface CaptchaRequestHandlerInterface
{
    /**
     * @param string $area
     * @param RequestInterface $request
     * @param HttpInterface $response
     * @param string $redirectOnFailureUrl
     * @return void
     * @throws LocalizedException
     */
    public function execute(
        string $area,
        RequestInterface $request,
        HttpInterface $response,
        string $redirectOnFailureUrl
    ): void;
}
