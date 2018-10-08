CREATE TABLE `#__djcf_profiles_msg` (
  `id` int(11) NOT NULL auto_increment,
  `user_to` int(11) NOT NULL,
  `user_from` int(11) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `custom_fields` text NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `#__djcf_emails` (`id`, `label`, `title`, `content`) VALUES
(34, 'COM_DJCLASSIFIEDS_ET_ASK_FORM_PROFILE_CONTACT', 'Profile Contact', '<p>Hello [[user_name]],</p>\r\n<p> </p>\r\n<p>Message from:</p>\r\n<p>User name: [[contact_author_name]]</p>\r\n<p>User email: [[contact_author_email]]</p>\r\n<p></p>\r\n[[contact_message]]');