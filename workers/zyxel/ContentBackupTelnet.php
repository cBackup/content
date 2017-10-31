<?php
/**
 * This file is part of cBackup, network equipment configuration backup tool
 * Copyright (C) 2017, Oļegs Čapligins, Imants Černovs, Dmitrijs Galočkins
 *
 * cBackup is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace app\modules\cds\content\workers\zyxel;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\workers\zyxel
 */
class ContentBackupTelnet extends ContentInstaller
{

    public function install()
    {

        if ($this->recordExists('{{%worker}}', ['name' => 'zyxel_backup'])) {
            throw new \Exception('Worker zyxel_backup already exists');
        }

        $this->command->insert('{{%worker}}', [
            'name'      => 'zyxel_backup',
            'task_name' => 'backup',
            'get'       => 'telnet'
        ])->execute();

        /** Get newly inserted worker id */
        $worker = $this->getEntryIdentifier('{{%worker}}', ['name'=> 'zyxel_backup'], 'id');

        /** Add worker jobs */
        $this->command->batchInsert('{{%job}}', ['name', 'worker_id', 'sequence_id', 'command_value', 'timeout', 'table_field'], [
            ['show_config', $worker, 1, 'show running-config', 60000, 'config'],
            ['logout', $worker, 2, 'logout', null, null],
        ])->execute();

        return true;

    }

}

