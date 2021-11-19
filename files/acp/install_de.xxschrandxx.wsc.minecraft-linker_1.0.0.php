<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\DatabaseTableChangeProcessor;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\WCF;

$tables = [
    // wcf1_user
    DatabaseTable::create('wcf1_user')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('minecraftUUIDs')
                ->defaultValue(0),
        ]),

    // wcf1_user_group
    DatabaseTable::create('wcf1_user_group')
        ->columns([
            BlobDatabaseTableColumn::create('minecraftGroupNames'),
        ]),

    // wcf1_user_minecraft
    DatabaseTable::create('wcf1_user_minecraft')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('minecraftID')
                ->autoIncrement(),
            NotNullInt10DatabaseTableColumn::create('userID'),
            VarcharDatabaseTableColumn::create('minecraftUUID')
                ->length(36)
                ->notNull(),
            VarcharDatabaseTableColumn::create('title')
                ->length(30),
            NotNullInt10DatabaseTableColumn::create('createdDate'),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['userID'])
                ->referencedTable('wcf1_user'),
        ]),
];

(new DatabaseTableChangeProcessor(
    $this->installation->getPackage(),
    $tables,
    WCF::getDB()->getEditor()
))->process();
