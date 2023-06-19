-- Update from phinx migration 20230115090200_add_user_coupon
ALTER TABLE detect_ban_log DROP FOREIGN KEY detect_ban_log_ibfk_1;
ALTER TABLE detect_log DROP FOREIGN KEY detect_log_ibfk_1;
ALTER TABLE detect_log DROP FOREIGN KEY detect_log_ibfk_2;
ALTER TABLE detect_log DROP FOREIGN KEY detect_log_ibfk_3;
ALTER TABLE detect_log DROP FOREIGN KEY detect_log_ibfk_5;
ALTER TABLE link DROP FOREIGN KEY link_ibfk_1;
ALTER TABLE login_ip DROP FOREIGN KEY login_ip_ibfk_1;
ALTER TABLE paylist DROP FOREIGN KEY paylist_ibfk_1;
ALTER TABLE user_hourly_usage DROP FOREIGN KEY user_hourly_usage_ibfk_1;
ALTER TABLE user_invite_code DROP FOREIGN KEY user_invite_code_ibfk_1;
ALTER TABLE user_subscribe_log DROP FOREIGN KEY user_subscribe_log_ibfk_1;