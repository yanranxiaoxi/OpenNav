<?php

require_once __DIR__ . '/../../Public/index.php';
require_once __DIR__ . '/Check.php';

if ($current_version === '0.1.4') {
	$helper->setGlobalConfig_AuthRequired('DATABASE_TYPE', DATABASE_TYPE, 'SQLite');
	@$database->query('DROP INDEX <on_categorys_fid_IDX>');
	@$database->query('DROP INDEX <on_categorys_id_IDX>');
	@$database->query('DROP INDEX <on_categorys_name_IDX>');
	@$database->query('DROP INDEX <on_categorys_weight_IDX>');
	@$database->query('ALTER TABLE <on_categorys> RENAME TO <on_categories>');
	@$database->query('CREATE INDEX <on_categories_fid_IDX> ON <on_categories> (<fid> ASC)');
	@$database->query('CREATE INDEX <on_categories_id_IDX> ON <on_categories> (<id> ASC)');
	@$database->query('CREATE INDEX <on_categories_title_IDX> ON <on_categories> (<title>)');
	@$database->query('CREATE INDEX <on_categories_weight_IDX> ON <on_categories> (<weight> DESC)');
	@$database->drop('on_database_upgrade_logs');
	@$database->delete('sqlite_sequence', ['name' => 'on_database_upgrade_logs']);
	unlink('../Data/Config.sample.php');
	unlink('../Data/Database.sample.db3');

	$helper->setGlobalConfig_AuthRequired('CONFIG_VERSION', CONFIG_VERSION, '0.1.5');
	$current_version = '0.1.5';
}
