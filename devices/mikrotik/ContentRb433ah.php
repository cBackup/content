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

namespace app\modules\cds\content\devices\mikrotik;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\devices\mikrotik
 */
class ContentRb433ah extends ContentInstaller
{

    public function install()
    {

        /** Insert auth template */
        if (!$this->recordExists('{{%device_auth_template}}', ['name' => 'mikrotik_auth'])) {
            $this->command->insert('{{%device_auth_template}}', [
                'name'          => 'mikrotik_auth',
                'auth_sequence' => ">",
                'description'   => 'Mikrotik authentication sequence'
            ])->execute();
        }

        /** Check if device exists */
        if ($this->recordExists('{{%device}}', ['vendor'=> 'Mikrotik', 'model' => 'RB433AH'])) {
            throw new \Exception('Device Mikrotik RB433AH already exists');
        }

        /** Insert new Device Mikrotik RB433AH  */
        $this->command->insert('{{%device}}', [
            'vendor'             => 'Mikrotik',
            'model'              => 'RB433AH',
            'auth_template_name' => 'mikrotik_auth'
        ])->execute();

        /** Get newly inserted device id */
        $device = $this->getEntryIdentifier('{{%device}}', ['vendor'=> 'Mikrotik', 'model' => 'RB433AH'], 'id');

        /** Add device attributes */
        $this->command->batchInsert('{{%device_attributes}}', ['device_id', 'sysobject_id', 'hw', 'sys_description'], [
            [$device, '1.3.6.1.4.1.14988.1', null, 'RouterOS RB433AH']
        ])->execute();

        return true;

    }

}
