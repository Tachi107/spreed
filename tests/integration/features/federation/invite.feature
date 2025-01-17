Feature: federation/invite
  Background:
    Given user "participant1" exists
    Given user "participant2" exists

  Scenario: federation is disabled
    Given the following app config is set
      | federation_enabled | no |
    Given user "participant1" creates room "room" (v4)
      | roomType | 3 |
      | roomName | room |
    And user "participant1" adds remote "participant2" to room "room" with 501 (v4)
    When user "participant1" sees the following attendees in room "room" with 200 (v4)
      | actorType  | actorId      | participantType |
      | users      | participant1 | 1               |

  Scenario: federation is enabled
    Given the following app config is set
      | federation_enabled | yes |
    Given user "participant1" creates room "room" (v4)
      | roomType | 3 |
      | roomName | room |
    And user "participant1" adds remote "participant2" to room "room" with 200 (v4)
    When user "participant1" sees the following attendees in room "room" with 200 (v4)
      | actorType       | actorId      | participantType |
      | users           | participant1 | 1               |
      | federated_users | participant2 | 3               |
