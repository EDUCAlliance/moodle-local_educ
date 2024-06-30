@educ @local_educ
Feature: New title added
  In order to make it clear
  As a user
  I should see a text that states that manual authentication is for visitors

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@example.com |

  @javascript
  Scenario: A non logged user should see the Log in using your visitor account
    Given I am on site homepage
    When I follow "Log in"
    Then I should see "Log in using your visitor account"
