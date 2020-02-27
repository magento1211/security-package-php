<?php
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Duo;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\TwoFactorAuth\TestFramework\TestCase\AbstractConfigureBackendController;

/**
 * Test for the DuoSecurity form.
 *
 * @magentoAppArea adminhtml
 */
class AuthTest extends AbstractConfigureBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/tfa/duo/auth';

    /**
     * @inheritDoc
     */
    protected $httpMethod = Request::METHOD_GET;

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key duo_security
     * @magentoConfigFixture default/twofactorauth/duo/secret_key duo_security
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname duo_security
     * @magentoConfigFixture default/twofactorauth/duo/application_key duo_security
     */
    public function testTokenAccess(): void
    {
        parent::testTokenAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key duo_security
     * @magentoConfigFixture default/twofactorauth/duo/secret_key duo_security
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname duo_security
     * @magentoConfigFixture default/twofactorauth/duo/application_key duo_security
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/twofactorauth/duo/integration_key duo_security
     * @magentoConfigFixture default/twofactorauth/duo/secret_key duo_security
     * @magentoConfigFixture default/twofactorauth/duo/api_hostname duo_security
     * @magentoConfigFixture default/twofactorauth/duo/application_key duo_security
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
