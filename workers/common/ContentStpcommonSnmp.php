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

namespace app\modules\cds\content\workers\common;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\workers\common
 */
class ContentStpcommonSnmp extends ContentInstaller
{

    /**
     * @return bool
     * @throws \Exception
     */
    public function install()
    {

        if ($this->recordExists('{{%worker}}', ['name' => 'stp_common'])) {
            throw new \Exception('Worker stp_common already exists');
        }

        $this->command->insert('{{%worker}}', [
            'name'      => 'stp_common',
            'task_name' => 'stp',
            'get'       => 'snmp'
        ])->execute();

        /** Get newly inserted worker id */
        $worker = $this->getEntryIdentifier('{{%worker}}', ['name'=> 'stp_common'], 'id');

        /** Add worker jobs */
        $this->command->batchInsert('{{%job}}', ['name', 'worker_id', 'sequence_id', 'command_value', 'command_var', 'snmp_request_type', 'table_field'], [
            ['get_node_mac',   $worker, 1, '1.3.6.1.2.1.17.1.1.0', null, 'get', 'node_mac'],
            ['get_root_mac',   $worker, 2, '1.3.6.1.2.1.17.2.5.0', null, 'get', 'root_mac'],
            ['get_root_port',  $worker, 3, '1.3.6.1.2.1.17.2.7.0', '%%ROOT_PORT%%', 'get', 'root_port'],
            ['get_bridge_mac', $worker, 4, '1.3.6.1.2.1.17.2.15.1.8.%%ROOT_PORT%%', null, 'get', 'bridge_mac'],
        ])->execute();

        return true;

    }

}
