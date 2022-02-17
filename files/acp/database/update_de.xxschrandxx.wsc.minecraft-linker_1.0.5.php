<?php

use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;

return [
    // wcf1_user_minecraft
    DatabaseTable::create('wcf1_user_minecraft')
        ->columns([
            VarcharDatabaseTableColumn::create('minecraftName')
                ->length(16)
        ])
];
