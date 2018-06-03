Feature: Manage files

  Scenario: load fixtures
        When I populate the files
        Then there should be 101 photos
        And there shoud be 9 directories
