Feature: Manage access and user

  Scenario: Make an OPTIONS request should be valid
    Given I send a "OPTIONS" request to "/anywhere"
    Then the response status code should be 200
    And the response should be empty
    And the header "cache-control" should be equal to "max-age=86400, public"

  Scenario: Getting user info while not authenticated should not work
    When I am on "/v1/me"
    Then the response status code should be 401

  Scenario: Getting user info while authenticated should work
    Given I am authenticated with user test
    When I send a GET request to "/v1/me"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON nodes should be equal to:
        | type     | user |
        | username | test |
    And the JSON node "roles" should have 1 element
    And the JSON node "roles[0]" should be equal to "ROLE_USER"

#   Scenario: If I send a non-GET request as a user, I should receive a 401 response
#     Given I am authenticated with user test
#     When I send a POST request to "/anywhere"
#     Then the response status code should be 401
