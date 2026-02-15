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
