<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\UserFactory;
use OTPHP\TOTP;

class GoogleActivateTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'twoFactorAuthGoogleActivateV1';
    const OPERATION = 'ActivateRequest';
    const RESOURCE_PATH = '/V1/tfa/provider/google/activate';

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfig;

    /**
     * @var Google
     */
    private $google;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->tokenManager = $objectManager->get(UserConfigTokenManagerInterface::class);
        $this->tfa = $objectManager->get(TfaInterface::class);
        $this->userFactory = $objectManager->get(UserFactory::class);
        $this->google = $objectManager->get(Google::class);
        $this->userConfig = $objectManager->get(UserConfigManagerInterface::class);
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testInvalidTfat()
    {
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => 'abc', 'otp' => 'invalid']);
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Invalid tfa token', $response['message']);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers duo_security
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testUnavailableProvider()
    {
        $userId = $this->getUserId();
        $token = $this->tokenManager->issueFor($userId);
        $serviceInfo = $this->buildServiceInfo();

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => $token, 'otp' => 'invalid']);
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Provider is not allowed.', $response['message']);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testAlreadyActivatedProvider()
    {
        $userId = $this->getUserId();
        $token = $this->tokenManager->issueFor($userId);
        $serviceInfo = $this->buildServiceInfo();
        $otp = $this->getUserOtp();
        $this->tfa->getProviderByCode(Google::CODE)
            ->activate($userId);

        try {
            $this->_webApiCall($serviceInfo, ['tfaToken' => $token, 'otp' => $otp]);
            self::fail('Endpoint should have thrown an exception');
        } catch (\Throwable $exception) {
            $response = json_decode($exception->getMessage(), true);
            self::assertEmpty(json_last_error());
            self::assertSame('Provider is already configured.', $response['message']);
        }
    }

    /**
     * @magentoConfigFixture twofactorauth/general/force_providers google
     * @magentoApiDataFixture Magento/User/_files/user_with_custom_role.php
     */
    public function testActivate()
    {
        $userId = $this->getUserId();
        $token = $this->tokenManager->issueFor($userId);
        $otp = $this->getUserOtp();
        $serviceInfo = $this->buildServiceInfo();

        $response = $this->_webApiCall(
            $serviceInfo,
            [
                'tfaToken' => $token,
                'otp' => $otp
            ]
        );
        self::assertNotEmpty($response);
        self::assertTrue($response);
    }

    private function getUserOtp(): string
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('customRoleUser');
        $totp = new TOTP($user->getEmail(), $this->google->getSecretCode($user));

        // Enable longer window of valid tokens to prevent test race condition
        $this->userConfig->addProviderConfig((int)$user->getId(), Google::CODE, ['window' => 120]);

        return $totp->now();
    }

    /**
     * @return array
     */
    private function buildServiceInfo(): array
    {
        return [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . self::OPERATION
            ]
        ];
    }

    private function getUserId(): int
    {
        $user = $this->userFactory->create();
        $user->loadByUsername('customRoleUser');

        return (int)$user->getId();
    }
}
