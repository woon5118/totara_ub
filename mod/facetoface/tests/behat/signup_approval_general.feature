@mod @mod_facetoface @totara
Feature: Manager approval
  In order to control seminar attendance
  As a manager
  I need to authorise seminar signups

  Background:
   Given I am on a totara site
   And the following "users" exist:
     | username    | firstname | lastname | email              |
     | sysapprover | Terry     | Ter      | terry@example.com  |
     | actapprover | Larry     | Lar      | larry@example.com  |
     | teacher     | Freddy    | Fred     | freddy@example.com |
     | trainer     | Benny     | Ben      | benny@example.com  |
     | manager     | Cassy     | Cas      | cassy@example.com  |
     | jimmy       | Jimmy     | Jim      | jimmy@example.com  |
     | timmy       | Timmy     | Tim      | timmy@example.com  |
     | sammy       | Sammy     | Sam      | sammy@example.com  |
     | sally       | Sally     | Sal      | sally@example.com  |
     #   And the following "courses" exist:
     #     | fullname                 | shortname | category |
     #     | Classroom Connect Course | CCC       | 0        |
     #   And the following "course enrolments" exist:
     #     | user    | course | role           |
     #     | teacher | CCC    | editingteacher |
     #     | trainer | CCC    | teacher        |
     #     | jimmy   | CCC    | student        |
     #     | timmy   | CCC    | student        |
     #     | sammy   | CCC    | student        |
     #     | sally   | CCC    | student        |
     #   And the following position assignments exist:
     #     | user  | manager |
     #     | jimmy | manager |
     #     | timmy | manager |
     #     | sammy | manager |

     # TODO - Switching approval types with pending requests.
     #
     # TODO - reportbuilder changes
     #
     #
