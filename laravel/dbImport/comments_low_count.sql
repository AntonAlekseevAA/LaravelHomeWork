DELETE FROM `comments` WHERE `id`=154;
INSERT INTO `comments` (`id`, `comment`, `votes`, `reply_id`, `users_id`, `created_at`, `updated_at`, `level`) VALUES (154, 'NestedNested1', 18, 156, 11, '2019-02-10 16:43:01', '2019-02-10 16:43:01', 1);
DELETE FROM `comments` WHERE `id`=158;
INSERT INTO `comments` (`id`, `comment`, `votes`, `reply_id`, `users_id`, `created_at`, `updated_at`, `level`) VALUES (158, 'NestedInNested', 65, 154, 11, '2019-02-10 19:52:32', '2019-02-10 19:52:33', 2);
DELETE FROM `comments` WHERE `id`=157;
INSERT INTO `comments` (`id`, `comment`, `votes`, `reply_id`, `users_id`, `created_at`, `updated_at`, `level`) VALUES (157, 'Nested2', 234, 156, 11, '2019-02-10 19:51:48', '2019-02-10 19:51:51', 1);
DELETE FROM `comments` WHERE `id`=156;
INSERT INTO `comments` (`id`, `comment`, `votes`, `reply_id`, `users_id`, `created_at`, `updated_at`, `level`) VALUES (156, 'Root1', 64, 0, 11, '2019-02-10 16:49:06', '2019-02-10 16:49:06', 0);
