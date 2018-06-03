Feature: Manage files

  Scenario: load fixtures
        When I populate the files
        Then there should be 101 photos
        And there shoud be 9 directories

  Scenario:
    Given I am authenticated with user test
    And I add accept header equal to "application/ld+json"
    When I send a GET request to "/v1/directories/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON response should match file "features/snapshots/directory.1.json"

    # And the JSON should be valid according to the schema "features/schema/directories.json"

