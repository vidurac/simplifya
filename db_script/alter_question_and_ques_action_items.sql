SET SQL_MODE='ALLOW_INVALID_DATES';
ALTER TABLE `questions` CHANGE `question` `question` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `question_action_items` CHANGE `name` `name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
