SET @sName = 'bx_contact';


-- SETTINGS
SET @iCategId = (SELECT `id` FROM `sys_options_categories` WHERE `name`=@sName LIMIT 1);

DELETE FROM `sys_options` WHERE `name` IN ('bx_contact_send_from_senders_email', 'bx_contact_add_reply_to');
INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `check`, `check_error`, `extra`, `order`) VALUES
('bx_contact_add_reply_to', '', @iCategId, '_bx_contact_option_add_reply_to', 'checkbox', '', '', '', 20);
