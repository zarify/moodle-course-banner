@local @local_coursebanner
Feature: Course banners
  In order to show banner images on course pages
  As an administrator
  I need to configure category and course banner URLs

  Scenario: Category banner appears on the course page
    Given the following "categories" exist:
      | name            | idnumber |
      | Banner Category | CAT1     |
    And the following "courses" exist:
      | fullname       | shortname | category |
      | Banner Course  | BC1       | CAT1     |
    And I log in as "admin"
    When I navigate to "Plugins > Local plugins > Manage course banners" in site administration
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Banner Category')]//input[starts-with(@id, 'caturl_')]" to "https://example.test/category.png"
    And I press "Save changes"
    And I am on the "BC1" "course" page
    Then the "src" attribute of "#local-coursebanner-img" "css_element" should contain "https://example.test/category.png"

  Scenario: Course override supersedes category banner
    Given the following "categories" exist:
      | name            | idnumber |
      | Banner Category | CAT1     |
    And the following "courses" exist:
      | fullname        | shortname | category |
      | Override Course | OC1       | CAT1     |
    And I log in as "admin"
    And I navigate to "Plugins > Local plugins > Manage course banners" in site administration
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Banner Category')]//input[starts-with(@id, 'caturl_')]" to "https://example.test/category.png"
    And I press "Save changes"
    And I am on the "OC1" "course editing" page
    And I expand all fieldsets
    And I set the field with xpath "//input[@id='id_local_coursebanner_url']" to "https://example.test/course.png"
    And I press "Save and display"
    Then the "src" attribute of "#local-coursebanner-img" "css_element" should contain "https://example.test/course.png"

  Scenario: Editing teacher can configure course override field
    Given the following "categories" exist:
      | name            | idnumber |
      | Banner Category | CAT1     |
    And the following "courses" exist:
      | fullname          | shortname | category |
      | Permission Course | PC1       | CAT1     |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | PC1    | editingteacher |
    And I log in as "teacher1"
    When I am on the "PC1" "course editing" page
    And I expand all fieldsets
    Then "//input[@id='id_local_coursebanner_url']" "xpath_element" should exist

  Scenario: Manager can access manage course banners page
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | One      | manager1@example.com |
    And the following "system role assigns" exist:
      | user     | role    |
      | manager1 | manager |
    And I log in as "manager1"
    When I visit "/local/coursebanner/manage.php"
    Then I should see "Manage course banners"

  Scenario: Admin can configure banner height in manage course banners
    Given I log in as "admin"
    When I navigate to "Plugins > Local plugins > Manage course banners" in site administration
    And I set the field with xpath "//input[@id='bannerheight']" to "120"
    And I press "Save changes"
    Then the field with xpath "//input[@id='bannerheight']" matches value "120"

  Scenario: User with site config but without manage capability cannot see manage course banners link
    Given the following "roles" exist:
      | name            | shortname         | description                                 | archetype |
      | Plugin viewer   | pluginviewer      | Can access admin tree but not manage plugin | manager   |
    Given the following "permission overrides" exist:
      | capability                | permission | role         | contextlevel | reference |
      | moodle/site:config        | Allow      | pluginviewer | System       |           |
      | local/coursebanner:manage | Prohibit   | pluginviewer | System       |           |
    And the following "users" exist:
      | username | firstname | lastname | email              |
      | user1    | User      | One      | user1@example.com  |
    And the following "system role assigns" exist:
      | user  | role         |
      | user1 | pluginviewer |
    And I log in as "user1"
    When I navigate to "Plugins > Local plugins" in site administration
    Then "//a[contains(@href, '/local/coursebanner/manage.php')]" "xpath_element" should not exist

  Scenario: Non-editing teacher with course update capability cannot configure course override field
    Given the following "categories" exist:
      | name                 | idnumber |
      | Non-editing Category | CAT2     |
    And the following "courses" exist:
      | fullname             | shortname | category |
      | Non-editing Course   | NEC1      | CAT2     |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher2 | Teacher   | Two      | teacher2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher2 | NEC1   | teacher |
    And the following "permission overrides" exist:
      | capability           | permission | role    | contextlevel | reference |
      | moodle/course:update | Allow      | teacher | Course       | NEC1      |
    And I log in as "teacher2"
    When I am on the "NEC1" "course editing" page
    And I expand all fieldsets
    Then "//input[@id='id_local_coursebanner_url']" "xpath_element" should not exist

  Scenario: Teacher-manager can edit course settings but cannot configure banner override without capability
    Given the following "categories" exist:
      | name                   | idnumber |
      | Teacher-manager Cat    | CAT3     |
    And the following "courses" exist:
      | fullname               | shortname | category |
      | Teacher-manager Course | TMC1      | CAT3     |
    And the following "roles" exist:
      | name            | shortname      | description                     | archetype |
      | Teacher manager | teachermanager | Can edit course settings only   | teacher   |
    And the following "permission overrides" exist:
      | capability           | permission | role           | contextlevel | reference |
      | moodle/course:update | Allow      | teachermanager | Course       | TMC1      |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | tm1      | Teacher   | Manager  | tm1@example.com      |
    And the following "course enrolments" exist:
      | user | course | role           |
      | tm1  | TMC1   | teachermanager |
    And I log in as "tm1"
    When I am on the "TMC1" "course editing" page
    And I expand all fieldsets
    Then "//input[@id='id_local_coursebanner_url']" "xpath_element" should not exist

  Scenario: Teacher-manager with explicit banner edit capability can configure course override field
    Given the following "categories" exist:
      | name                      | idnumber |
      | Teacher-manager Allow Cat | CAT4     |
    And the following "courses" exist:
      | fullname                    | shortname | category |
      | Teacher-manager Allow Course | TMA1      | CAT4     |
    And the following "roles" exist:
      | name                  | shortname           | description                               | archetype |
      | Teacher manager allow | teachermanagerallow | Can edit course settings and banner field | teacher   |
    And the following "permission overrides" exist:
      | capability              | permission | role                | contextlevel | reference |
      | moodle/course:update    | Allow      | teachermanagerallow | Course       | TMA1      |
      | local/coursebanner:edit | Allow      | teachermanagerallow | Course       | TMA1      |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | tm2      | Teacher   | Manager2 | tm2@example.com |
    And the following "course enrolments" exist:
      | user | course | role              |
      | tm2  | TMA1   | teachermanagerallow |
    And I log in as "tm2"
    When I am on the "TMA1" "course editing" page
    And I expand all fieldsets
    Then "//input[@id='id_local_coursebanner_url']" "xpath_element" should exist
