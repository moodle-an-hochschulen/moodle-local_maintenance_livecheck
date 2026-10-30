@local @local_maintenance_livecheck @javascript
Feature: Configuring the maintenance_livecheck plugin
  In order to show the maintenance warning without a page refresh
  As admin
  I need to be able to configure the maintenance_livecheck plugin

  Background:
    Given the following config values are set as admin:
      | config        | value | plugin                      |
      | enable        | 1     | local_maintenance_livecheck |
      | checkinterval | 10    | local_maintenance_livecheck |

  Scenario: Enable maintenance_livecheck, reduce the interval and enable maintenance mode
    When I log in as "admin"
    And the following config values are set as admin:
      | maintenance_later | 1 |
    And I wait "15" seconds
    Then "#maintenance_announcement .maintenancewarning" "css_element" should exist

  Scenario: Enable maintenance_livecheck and check that the maintenance warning is also shown on the login page
    When I visit "/login/index.php"
    And the following config values are set as admin:
      | maintenance_later | 1 |
    And I wait "15" seconds
    Then "#maintenance_announcement .maintenancewarning" "css_element" should exist

  Scenario: Enable maintenance_livecheck and check that the maintenance warning is also shown on pages with a popup layout
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "activities" exist:
      | activity | name    | course | idnumber |
      | qbank    | Qbank 1 | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name                          |
      | Test questions   | numerical | Test question to be previewed |
    When I am on the "Test question to be previewed" "core_question > preview" page logged in as admin
    And the following config values are set as admin:
      | maintenance_later | 1 |
    And I wait "15" seconds
    Then "#maintenance_announcement .maintenancewarning" "css_element" should exist
