@local @local_maintenance_livecheck @javascript
Feature: Configuring the maintenance_livecheck plugin
  In order to show the maintenance warning without a page refresh
  As admin
  I need to be able to configure the maintenance_livecheck plugin

  Scenario: Enable maintenance_livecheck, reduce the interval and enable maintenance mode
    Given the following config values are set as admin:
      | config        | value | plugin                      |
      | enable        | 1     | local_maintenance_livecheck |
      | checkinterval | 10    | local_maintenance_livecheck |
    When I log in as "admin"
    And the following config values are set as admin:
      | maintenance_later | 1 |
    And I wait "15" seconds
    Then "#maintenance_announcement .maintenancewarning" "css_element" should exist
