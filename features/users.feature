# This file contains a user story for demonstration only.
# Learn how to get started with Behat and BDD on Behat's website:
# http://behat.org/en/latest/quick_start.html

Feature:

  Scenario: Getting user info while not authenticated should not work
    When I am on "/v1/me"
    Then the response status code should be 401

  Scenario: Getting user info while authenticated should work
    Given I add Authorization header equal to 'Bearer admin_unlimited_token'
    Given I am authenticated with user test
    When I add toto header equal to tutu
    And I send a GET request to "/v1/me"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON nodes should be equal to:
        | type     | user |
        | username | test |
    And the JSON node "roles" should have 1 element
    And the JSON node "roles[0]" should be equal to "ROLE_USER"
