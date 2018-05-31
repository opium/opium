<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext implements Context
{
    /**
     * @var Request
     */
    private $restContext;

    /**
     * @Given I am authenticated with user :username
     */
    public function iAmAuthenticatedWithUser(string $username)
    {
        $this->restContext->iAddHeaderEqualTo(
            'Authorization',
            sprintf('Basic %s', base64_encode($username . ':' . $username))
        );
    }


    /**
     * gatherContexts
     *
     * @param BeforeScenarioScope $scope
     *
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        if (!$environment instanceof InitializedContextEnvironment) {
            return;
        }
        $this->restContext = $environment->getContext(RestContext::class);
    }
}

