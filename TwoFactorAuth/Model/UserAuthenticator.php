<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

/**
 * Retrieves users from credentials and enforced throttling
 */
class UserAuthenticator
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserResource
     */
    private $userResource;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param UserFactory $userFactory
     * @param UserResource $userResource
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param TfaInterface $tfa
     * @param Json $json
     */
    public function __construct(
        UserFactory $userFactory,
        UserResource $userResource,
        UserConfigTokenManagerInterface $tokenManager,
        TfaInterface $tfa,
        Json $json
    ) {
        $this->userFactory = $userFactory;
        $this->userResource = $userResource;
        $this->tfa = $tfa;
        $this->tokenManager = $tokenManager;
        $this->json = $json;
    }

    /**
     * Obtain a user with an id and a tfa token
     *
     * @param string $tfaToken
     * @param string $providerCode
     * @return User
     * @throws AuthorizationException
     * @throws WebApiException
     */
    public function authenticateWithTokenAndProvider(string $tfaToken, string $providerCode): User
    {
        try {
            ['user_id' => $userId] = $this->json->unserialize(explode('.', base64_decode($tfaToken))[0]);
        } catch (\Exception $e) {
            throw new AuthorizationException(
                __('Invalid tfa token')
            );
        }

        if (!$this->tfa->getProviderIsAllowed($userId, $providerCode)) {
            throw new WebApiException(__('Provider is not allowed.'));
        } elseif ($this->tfa->getProviderByCode($providerCode)->isActive($userId)) {
            throw new WebApiException(__('Provider is already configured.'));
        } elseif (!$this->tokenManager->isValidFor($userId, $tfaToken)) {
            throw new AuthorizationException(
                __('Invalid tfa token')
            );
        }

        $user = $this->userFactory->create();
        $this->userResource->load($user, $userId);

        return $user;
    }
}
