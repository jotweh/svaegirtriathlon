CREATE  TABLE IF NOT EXISTS `#__mailster_lists` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `admin_mail` VARCHAR(255) NULL ,
  `published` TINYINT(1) NULL ,
  `active` TINYINT(1) NULL ,
  `public_registration` TINYINT(1) NULL ,
  `sending_public` TINYINT(1) NULL DEFAULT 1,
  `sending_recipients` TINYINT(1) NULL DEFAULT 0,
  `sending_admin` TINYINT(1) NULL DEFAULT 0,
  `sending_group` TINYINT(1) NULL DEFAULT 0,
  `sending_group_id` INT NULL,
  `disable_mail_footer` TINYINT(1) NULL , 
  `allow_registration` TINYINT(1) NULL ,
  `reply_to_sender` TINYINT(1) NULL ,
  `copy_to_sender` TINYINT(1) NULL ,
  `list_mail` VARCHAR(255) NULL ,
  `subject_prefix` VARCHAR(45) NULL ,
  `use_joomla_mailer` TINYINT(1) NULL , 
  `mail_in_user` VARCHAR(255) NULL ,
  `mail_in_pw` VARCHAR(45) NULL ,
  `mail_in_host` VARCHAR(45) NULL ,
  `mail_in_port` INT NULL ,
  `mail_in_use_secure` VARCHAR(45) NULL ,
  `mail_in_use_sec_auth` TINYINT(1) NULL ,
  `mail_in_protocol` VARCHAR(45) NULL ,
  `mail_in_params` VARCHAR(45) NULL ,
  `mail_out_user` VARCHAR(255) NULL ,
  `mail_out_pw` VARCHAR(45) NULL ,
  `mail_out_host` VARCHAR(45) NULL ,
  `mail_out_port` INT NULL ,
  `mail_out_use_secure` VARCHAR(45) NULL ,
  `mail_out_use_sec_auth` TINYINT(1) NULL ,
  `custom_header_plain` TEXT NULL ,
  `custom_footer_plain` TEXT NULL ,
  `custom_header_html` TEXT NULL ,
  `custom_footer_html` TEXT NULL ,
  `bcc_count` INT NULL ,
  `alibi_to_mail` VARCHAR(255) NULL ,
  `addressing_mode` TINYINT(1) NULL DEFAULT 1, 
  `mail_from_mode` TINYINT(1) NULL DEFAULT 0, 
  `name_from_mode` TINYINT(1) NULL DEFAULT 0,   
  `archive_mode` INT NULL DEFAULT 0, 
  `bounce_mode` INT NULL DEFAULT 0, 
  `bounce_mail` VARCHAR(255) NULL ,
  `mail_format_conv` INT NULL DEFAULT 1,
  `mail_format_altbody` TINYINT(1) NULL DEFAULT 1,
  `max_send_attempts` INT NULL ,
  `filter_mails` TINYINT(1) NULL ,
  `clean_up_subject` TINYINT(1) NULL ,  
  `lock_id` INT NULL ,
  `is_locked` TINYINT(1) DEFAULT 0 ,  
  `last_lock` TIMESTAMP NULL ,
  `last_check` TIMESTAMP NULL ,
  `throttle_hour` TINYINT(2) NULL DEFAULT 0,
  `throttle_hour_cr` INT NULL DEFAULT 0,
  `throttle_hour_limit` INT NULL DEFAULT 0,
  `cstate` INT NULL DEFAULT 0,
  `mail_size_limit` INT NULL DEFAULT 0,
  `notify_not_fwd_sender` TINYINT(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`)
);

CREATE  TABLE IF NOT EXISTS `#__mailster_users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `email` VARCHAR(255) NULL ,
  `notes` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`)
);

CREATE  TABLE IF NOT EXISTS `#__mailster_list_members` (
  `list_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `is_joomla_user` TINYINT(1) NULL ,
  `receive_mails` TINYINT(1) NULL ,
  `send_mails` TINYINT(1) NULL
);

CREATE  TABLE IF NOT EXISTS `#__mailster_mails` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `list_id` INT NOT NULL ,
  `thread_id` INT NOT NULL ,
  `hashkey` VARCHAR(45) NULL ,
  `message_id` VARCHAR(255) NULL ,
  `in_reply_to` VARCHAR(255) NULL ,
  `references_to` VARCHAR(255) NULL ,
  `receive_timestamp` TIMESTAMP NULL ,
  `from_name` VARCHAR(255) NULL ,
  `from_email` VARCHAR(255) NULL ,
  `subject` VARCHAR(255) NULL ,
  `body` TEXT NULL ,
  `html` TEXT NULL ,
  `has_attachments` TINYINT(1) NULL ,
  `fwd_errors` TINYINT(1) NULL ,
  `fwd_completed` TINYINT(1) NULL ,
  `fwd_completed_timestamp` TIMESTAMP NULL ,
  `blocked_mail` TINYINT(1) NULL ,
  `bounced_mail` TINYINT(1) NULL ,
  `no_content` TINYINT(1) NULL DEFAULT 0 ,  
  PRIMARY KEY (`id`)
);

CREATE  TABLE IF NOT EXISTS `#__mailster_groups` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`)
);


CREATE  TABLE IF NOT EXISTS `#__mailster_list_groups` (
  `list_id` INT NOT NULL ,
  `group_id` INT NOT NULL ,
  `receive_mails` TINYINT(1) NULL ,
  `send_mails` TINYINT(1) NULL
);

CREATE  TABLE IF NOT EXISTS `#__mailster_group_users` (
  `group_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `is_joomla_user` TINYINT(1) NULL
);

CREATE  TABLE IF NOT EXISTS `#__mailster_queued_mails` (
  `mail_id` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  `email` VARCHAR(255) NOT NULL,
  `error_count` INT NULL,
  `lock_id` INT NULL DEFAULT 0,
  `is_locked` TINYINT(1) NULL DEFAULT 0,
  `last_lock` TIMESTAMP NULL
);

CREATE  TABLE IF NOT EXISTS `#__mailster_threads` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `first_mail_id` INT NOT NULL ,
  `last_mail_id` INT NOT NULL ,
  `ref_message_id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`)
);

CREATE  TABLE IF NOT EXISTS `#__mailster_attachments` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `mail_id` INT NOT NULL ,
  `filename` VARCHAR(255) NOT NULL,
  `filepath` VARCHAR(255) NOT NULL,
  `content_id` VARCHAR(255) NULL,
  `disposition` TINYINT(1) NOT NULL,
  `type` INT NOT NULL,
  `subtype` VARCHAR(45) NULL,
  `params` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
);

CREATE  TABLE IF NOT EXISTS `#__mailster_notifies` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `notify_type` INT NOT NULL ,
  `trigger_type` INT NOT NULL ,
  `target_type` INT NOT NULL ,
  `list_id` INT NULL ,
  `group_id` INT NULL ,
  `user_id` INT NULL ,
  PRIMARY KEY (`id`)
);

CREATE  TABLE IF NOT EXISTS `#__mailster_log` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `level` INT NOT NULL DEFAULT 0 ,
  `type` INT NOT NULL DEFAULT 0 ,
  `log_time` TIMESTAMP NOT NULL ,
  `msg` TEXT NULL ,
  PRIMARY KEY (`id`)
);
 
