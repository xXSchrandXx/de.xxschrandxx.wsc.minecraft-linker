<?php

use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    // wcf1_user_minecraft
    PartialDatabaseTable::create('wcf1_user_minecraft')
        ->columns([
            VarcharDatabaseTableColumn::create('minecraftName')
                ->length(16)
        ])
];
