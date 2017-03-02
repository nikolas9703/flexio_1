<?php

use \Flexio\Migration\Migration;

class CoreV3 extends Migration
{
    public function up()
    {
        $this->dropTable('ci_sessions');
        $this->execute("CREATE TABLE IF NOT EXISTS `ci_sessions` (
            `id` varchar(128) NOT NULL,
            `ip_address` varchar(45) NOT NULL,
            `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
            `data` blob NOT NULL,
            KEY `ci_sessions_timestamp` (`timestamp`)
        )");
    }

    public function down()
    {
        $this->dropTable('ci_sessions');
        $this->execute("CREATE TABLE IF NOT EXISTS `ci_sessions` (
            `id` varchar(128) NOT NULL,
            `ip_address` varchar(45) NOT NULL,
            `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
            `data` blob NOT NULL,
            KEY `ci_sessions_timestamp` (`timestamp`)
        )");
    }
}
