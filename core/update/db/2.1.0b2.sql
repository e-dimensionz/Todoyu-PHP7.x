/* project */

UPDATE `system_right` SET `right` = 'project:planning:see' WHERE `right` = 'projectstatus:planning:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'project:progress:see' WHERE `right` = 'projectstatus:progress:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'project:done:see' WHERE `right` = 'projectstatus:done:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'project:cleared:see' WHERE `right` = 'projectstatus:cleared:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'project:warranty:see' WHERE `right` = 'projectstatus:warranty:see' AND `ext` = 112;

/* see task  */

UPDATE `system_right` SET `right` = 'seetask:seeAll' WHERE `right` = 'task:seeAll' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:planning:see' WHERE `right` = 'taskstatus:planning:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:open:see' WHERE `right` = 'taskstatus:open:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:progress:see' WHERE `right` = 'taskstatus:progress:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:confirm:see' WHERE `right` = 'taskstatus:confirm:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:done:see' WHERE `right` = 'taskstatus:done:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:accepted:see' WHERE `right` = 'taskstatus:accepted:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:rejected:see' WHERE `right` = 'taskstatus:rejected:see' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'seetask:cleared:see' WHERE `right` = 'taskstatus:cleared:see' AND `ext` = 112;

/* add task */

UPDATE `system_right` SET `right` = 'addtask:addTaskInOwnProjects' WHERE `right` = 'task:addInOwnProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:addViaQuickCreateHeadlet' WHERE `right` = 'task:addViaQuickCreateHeadlet' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:addTaskInAllProjects' WHERE `right` = 'task:addInAllProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:addContainerInOwnProjects' WHERE `right` = 'container:addInOwnProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:addContainerInAllProjects' WHERE `right` = 'container:addInAllProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:planning:create' WHERE `right` = 'taskstatus:planning:create' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:open:create' WHERE `right` = 'taskstatus:open:create' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:progress:create' WHERE `right` = 'taskstatus:progress:create' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:confirm:create' WHERE `right` = 'taskstatus:confirm:create' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:done:create' WHERE `right` = 'taskstatus:done:create' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:accepted:create' WHERE `right` = 'taskstatus:accepted:create' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:rejected:create' WHERE `right` = 'taskstatus:rejected:create' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'addtask:cleared:create' WHERE `right` = 'taskstatus:cleared:create' AND `ext` = 112;

/*  edit task */

UPDATE `system_right` SET `right` = 'edittask:editOwnTasks' WHERE `right` = 'task:editOwnTasks' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:editTaskInOwnProjects' WHERE `right` = 'task:editInOwnProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:editTaskInAllProjects' WHERE `right` = 'task:editInAllProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:editOwnContainers' WHERE `right` = 'container:editOwnContainers' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:editContainerInOwnProjects' WHERE `right` = 'container:editInOwnProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:editContainerInAllProjects' WHERE `right` = 'container:editInAllProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:planning:edit' WHERE `right` = 'taskstatus:planning:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:open:edit' WHERE `right` = 'taskstatus:open:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:progress:edit' WHERE `right` = 'taskstatus:progress:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:confirm:edit' WHERE `right` = 'taskstatus:confirm:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:done:edit' WHERE `right` = 'taskstatus:done:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:accepted:edit' WHERE `right` = 'taskstatus:accepted:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:rejected:edit' WHERE `right` = 'taskstatus:rejected:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:cleared:edit' WHERE `right` = 'taskstatus:cleared:edit' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittask:useTaskAndContainerClipboard' WHERE `right` = 'clipboard:useTaskAndContainerClipboard' AND `ext` = 112;

/* edit task detail */


UPDATE `system_right` SET `right` = 'edittaskdetail:editDateStart' WHERE `right` = 'task:editDateStart' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:editDateEnd' WHERE `right` = 'task:editDateEnd' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:editDeadline' WHERE `right` = 'task:editDeadline' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:editPersonAssigned' WHERE `right` = 'task:editPersonAssigned' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:editPersonOwner' WHERE `right` = 'task:editPersonOwner' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:editActivity' WHERE `right` = 'task:editActivity' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:editEstimatedWorkload' WHERE `right` = 'task:editEstimatedWorkload' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:editIsPublic' WHERE `right` = 'task:editIsPublic' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:planning:changeto' WHERE `right` = 'taskstatus:planning:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:open:changeto' WHERE `right` = 'taskstatus:open:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:progress:changeto' WHERE `right` = 'taskstatus:progress:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:confirm:changeto' WHERE `right` = 'taskstatus:confirm:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:done:changeto' WHERE `right` = 'taskstatus:done:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:accepted:changeto' WHERE `right` = 'taskstatus:accepted:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:rejected:changeto' WHERE `right` = 'taskstatus:rejected:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:cleared:changeto' WHERE `right` = 'taskstatus:cleared:changeto' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:planning:changefrom' WHERE `right` = 'taskstatus:planning:changefrom' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:open:changefrom' WHERE `right` = 'taskstatus:open:changefrom' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:progress:changefrom' WHERE `right` = 'taskstatus:progress:changefrom' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:confirm:changefrom' WHERE `right` = 'taskstatus:confirm:changefrom' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:done:changefrom' WHERE `right` = 'taskstatus:done:changefrom' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:accepted:changefrom' WHERE `right` = 'taskstatus:accepted:changefrom' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:rejected:changefrom' WHERE `right` = 'taskstatus:rejected:changefrom' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'edittaskdetail:cleared:changefrom' WHERE `right` = 'taskstatus:cleared:changefrom' AND `ext` = 112;

/* delete task */

UPDATE `system_right` SET `right` = 'deletetask:deleteOwnTasks' WHERE `right` = 'task:deleteOwnTasks' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'deletetask:deleteTaskInOwnProjects' WHERE `right` = 'task:deleteInOwnProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'deletetask:deleteTaskInAllProjects' WHERE `right` = 'task:deleteInAllProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'deletetask:deleteOwnContainers' WHERE `right` = 'container:deleteOwnContainers' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'deletetask:deleteContainerInOwnProjects' WHERE `right` = 'container:deleteInOwnProjects' AND `ext` = 112;
UPDATE `system_right` SET `right` = 'deletetask:deleteContainerInAllProjects' WHERE `right` = 'container:deleteInAllProjects' AND `ext` = 112;


-- change end date field of containers from date_end to date_deadline
UPDATE `ext_project_task` SET `date_deadline` = `date_end` WHERE `type` = 2 AND `date_deadline` = 0 AND `date_end` != 0;