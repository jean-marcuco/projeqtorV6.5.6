-- ///////////////////////////////////////////////////////////
-- // PROJECTOR                                             //
-- //-------------------------------------------------------//
-- // Version : 6.5.1 specific for postgresql               //
-- // Date : 2017-12-12                                     //
-- ///////////////////////////////////////////////////////////

UPDATE `${prefix}report` SET `sortOrder`=283 WHERE `id`=4;
UPDATE `${prefix}report` SET `sortOrder`=284 WHERE `id`=60;

UPDATE `${prefix}menu` set `level`='Project' where `id` in (181, 182);

UPDATE `${prefix}reportparameter` set `defaultValue`=null where `idReport` in (76, 77) and `name`='idResource';

-- Consistency 

DELETE FROM `${prefix}planningelement` WHERE refType='Activity' and refId not in (select id from `${prefix}activity`);
DELETE FROM `${prefix}planningelement` WHERE refType='Meeting' and refId not in (select id from `${prefix}meeting`);
DELETE FROM `${prefix}planningelement` WHERE refType='Milestone' and refId not in (select id from `${prefix}milestone`);
DELETE FROM `${prefix}planningelement` WHERE refType='PeriodicMeeting' and refId not in (select id from `${prefix}periodicmeeting`);
DELETE FROM `${prefix}planningelement` WHERE refType='Project' and refId not in (select id from `${prefix}project`);
DELETE FROM `${prefix}planningelement` WHERE refType='TestSession' and refId not in (select id from `${prefix}testsession`);

DELETE FROM `${prefix}workelement` WHERE refType='Ticket' and refId not in (select id from `${prefix}ticket`);