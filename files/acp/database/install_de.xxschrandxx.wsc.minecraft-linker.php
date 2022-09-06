<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\PartialDatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;

return [
    // wcf1_user_group
    PartialDatabaseTable::create('wcf1_user_group')
        ->columns([
            BlobDatabaseTableColumn::create('minecraftGroupNames'),
        ]),

    // wcf1_user_minecraft
    DatabaseTable::create('wcf1_user_minecraft')
        ->columns([
            ObjectIdDatabaseTableColumn::create('minecraftUserID'),
            VarcharDatabaseTableColumn::create('title')
                ->length(16),
            VarcharDatabaseTableColumn::create('minecraftUUID')
                ->length(36)
                ->notNull(),
            VarcharDatabaseTableColumn::create('minecraftName')
                ->length(16)
                ->notNull(),
            VarcharDatabaseTableColumn::create('code')
                ->length(16)
                ->notNull(),
            IntDatabaseTableColumn::create('createdDate')
                ->length(10),
        ]),

    // wcf1_user_to_user_minecraft
    DatabaseTable::create('wcf1_user_to_user_minecraft')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('userID'),
            ObjectIdDatabaseTableColumn::create('minecraftUserID')
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['userID'])
                ->referencedTable('wcf1_user'),
            DatabaseTableForeignKey::create()
            ->columns(['minecraftUserID'])
            ->onDelete('CASCADE')
            ->referencedColumns(['minecraftUserID'])
            ->referencedTable('wcf1_user_minecraft')
        ])
];
