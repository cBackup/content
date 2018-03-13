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

namespace app\modules\cds\content\workers\dlink;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\workers\dlink
 */
class ContentBackupTelnet extends ContentInstaller
{

    /**
     * @return bool
     * @throws \Exception
     */
    public function install()
    {

        if ($this->recordExists('{{%worker}}', ['name' => 'd_link_backup'])) {
            throw new \Exception('Worker d_link_backup already exists');
        }

        $this->command->insert('{{%worker}}', [
            'name'      => 'd_link_backup',
            'task_name' => 'backup',
            'get'       => 'telnet'
        ])->execute();

        /** Get newly inserted worker id */
        $worker = $this->getEntryIdentifier('{{%worker}}', ['name'=> 'd_link_backup'], 'id');

        /** Add worker jobs */
        $this->command->batchInsert('{{%job}}', ['name', 'worker_id', 'sequence_id', 'command_value', 'timeout', 'table_field'], [
            ['disable_clipaging', $worker, 1, 'disable clipaging', null, null],
            ['show_config', $worker, 2, 'show config current_config', 60000, 'config'],
            ['enable_clipaging', $worker, 3, 'enable clipaging', null, null],
            ['logout', $worker, 4, 'logout', null, null],
        ])->execute();

        return true;

    }

}

