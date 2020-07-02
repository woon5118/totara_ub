  --  //nocommit[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] SELECT "pi".* FROM mdl_perform_participant_instance "pi" INNER JOIN mdl_perform_participant_section "ps" ON "pi".id = "ps".participant_instance_id WHERE "pi".subject_instance_id = '3' AND pi.id <> '3' AND ps.section_id = '1'
[07-Jul-2020 11:03:00 Pacific/Auckland] [array (
  0 => '3',
  1 => '3',
  2 => '1',
)]
[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] Query took: 0.0039498805999756 seconds.

[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] SELECT "totara_core_relationship".* FROM mdl_totara_core_relationship "totara_core_relationship" WHERE "totara_core_relationship".id = '2'
[07-Jul-2020 11:03:00 Pacific/Auckland] [array (
  0 => '2',
)]
[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] Query took: 0.0022079944610596 seconds.

[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] SELECT "totara_core_relationship_resolver".* FROM mdl_totara_core_relationship_resolver "totara_core_relationship_resolver" WHERE "totara_core_relationship_resolver".relationship_id = '2'
[07-Jul-2020 11:03:00 Pacific/Auckland] [array (
  0 => 2,
)]
[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] Query took: 0.0021331310272217 seconds.

[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] SELECT "user".* FROM mdl_user "user" WHERE "user".id = '21'
[07-Jul-2020 11:03:00 Pacific/Auckland] [array (
  0 => '21',
)]
[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
[07-Jul-2020 11:03:00 Pacific/Auckland] Query took: 0.0021238327026367 seconds.

[07-Jul-2020 11:03:00 Pacific/Auckland] --------------------------------
