<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\PartialDatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

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
            VarcharDatabaseTableColumn::create('minecraftUUID')
                ->length(36)
                ->notNull(),
            VarcharDatabaseTableColumn::create('minecraftName')
                ->length(16)
                ->notNull(),
            NotNullInt10DatabaseTableColumn::create('createdDate'),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['userID'])
                ->referencedTable('wcf1_user')
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['minecraftUserID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['minecraftUserID'])
                ->referencedTable('wcf1_user_to_user_minecraft')

        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['minecraftUserID'])
        ]),
    
    // wcf1_user_to_user_minecraft
    DatabaseTable::create('wcf1_user_to_user_minecraft')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('userID'),
            NotNullInt10DatabaseTableColumn::create('minecraftUserID'),
            VarcharDatabaseTableColumn::create('title')
                ->notNull(),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['userID'])
                ->referencedTable('wcf1_user'),
        ])
];
