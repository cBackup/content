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

namespace app\modules\cds\content\devices\dlink;

use app\modules\cds\components\ContentInstaller;

class ContentDES320028f extends ContentInstaller
{

    public function install()
    {

        /** Insert auth template */
        if (!$this->recordExists('{{%device_auth_template}}', ['name' => 'd_link_auth'])) {
            $this->command->insert('{{%device_auth_template}}', [
                'name'          => 'd_link_auth',
                'auth_sequence' => "ame:\n{{telnet_login}}\nord:\n{{telnet_password}}\n#",
                'description'   => 'D-Link authentication sequence'
            ])->execute();
        }

        /** Check if device exists */
        if ($this->recordExists('{{%device}}', ['vendor'=> 'Dlink', 'model' => 'DES_3200_28F'])) {
            throw new \Exception('Device Dlink DES_3200_28F already exists');
        }

        /** Insert new Device Dlink DES_3200_28F  */
        $this->command->insert('{{%device}}', [
            'vendor'             => 'Dlink',
            'model'              => 'DES_3200_28F',
            'auth_template_name' => 'd_link_auth'
        ])->execute();

        /** Get newly inserted device id */
        $device = $this->getEntryIdentifier('{{%device}}', ['vendor'=> 'Dlink', 'model' => 'DES_3200_28F'], 'id');

        /** Add device attributes */
        $this->command->batchInsert('{{%device_attributes}}', ['device_id', 'sysobject_id', 'hw', 'sys_description'], [
            [$device, '1.3.6.1.4.1.171.10.113.1.4', 'A1', 'D-Link DES-3200-28F Fast Ethernet Switch'],
            [$device, '1.3.6.1.4.1.171.10.113.6.1', 'C1', 'DES-3200-28F Fast Ethernet Switch']
        ])->execute();

        return true;

    }

}
