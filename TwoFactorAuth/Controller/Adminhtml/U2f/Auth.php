<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\U2f;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\User\Model\User;

/**
 * UbiKey authentication controller
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Auth extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @param Action\Context $context
     * @param Session $session
     * @param PageFactory $pageFactory
     * @param UserConfigManagerInterface $userConfigManager
     * @param TfaInterface $tfa
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        PageFactory $pageFactory,
        UserConfigManagerInterface $userConfigManager,
        TfaInterface $tfa
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->pageFactory = $pageFactory;
        $this->userConfigManager = $userConfigManager;
    }

    /**
     * Get current user
     * @return User|null
     */
    private function getUser(): ?User
    {
        return $this->session->getUser();
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->userConfigManager->setDefaultProvider((int) $this->getUser()->getId(), U2fKey::CODE);
        return $this->pageFactory->create();
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $user = $this->getUser();

        return
            $user &&
            $this->tfa->getProviderIsAllowed((int) $this->getUser()->getId(), U2fKey::CODE) &&
            $this->tfa->getProvider(U2fKey::CODE)->isActive((int) $this->getUser()->getId());
    }
}
