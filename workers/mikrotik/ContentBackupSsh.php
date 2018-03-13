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

namespace app\modules\cds\content\workers\mikrotik;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\workers\mikrotik
 */
class ContentBackupSsh extends ContentInstaller
{

    /**
     * @return bool
     * @throws \Exception
     */
    public function install()
    {

        if ($this->recordExists('{{%worker}}', ['name' => 'mikrotik_backup'])) {
            throw new \Exception('Worker mikrotik_backup already exists');
        }

        $this->command->insert('{{%worker}}', [
            'name'      => 'mikrotik_backup',
            'task_name' => 'backup',
            'get'       => 'ssh'
        ])->execute();

        /** Get newly inserted worker id */
        $worker = $this->getEntryIdentifier('{{%worker}}', ['name'=> 'mikrotik_backup'], 'id');

        /** Add worker jobs */
        $this->command->batchInsert('{{%job}}', ['name', 'worker_id', 'sequence_id', 'command_value', 'timeout', 'table_field'], [
            ['show_config', $worker, 1, 'export', 60000, 'config'],
            ['quit', $worker, 2, 'quit', null, null],
        ])->execute();

        return true;

    }

}

