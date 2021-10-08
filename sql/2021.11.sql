ALTER TABLE node ADD COLUMN `custom_config` text NOT NULL DEFAULT '{}';
ALTER TABLE node DROP COLUMN `method`;
ALTER TABLE node DROP COLUMN `custom_method`;
ALTER TABLE node DROP COLUMN `custom_rss`;
