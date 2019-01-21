DROP TABLE invite_code;

ALTER TABLE `user`
  DROP `node_class`,
  DROP `node_speed`,
  DROP `node_period`;
  
DROP TABLE ss_reset_pwd;

ALTER TABLE `ss_node`
  DROP `offset`,
  DROP `node_speed_sum`,
  DROP `node_ping`;