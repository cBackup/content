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

namespace app\modules\cds\content\devices\cisco;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\devices\cisco
 */
class ContentC4500 extends ContentInstaller
{

    public function install()
    {

        /** Insert auth template */
        if (!$this->recordExists('{{%device_auth_template}}', ['name' => 'cisco_auth_with_password'])) {
            $this->command->insert('{{%device_auth_template}}', [
                'name'          => 'cisco_auth_with_password',
                'auth_sequence' => "in:\n{{telnet_login}}\nord:\n{{telnet_password}}\n>\nena\nord:\n{{enable_password}}\n#",
                'description'   => 'Cisco authentication sequence with enable password'
            ])->execute();
        }

        /** Check if vendor exists */
        if (!$this->recordExists('{{%vendor}}', ['name'=> 'Cisco'])) {
            $this->command->insert('{{%vendor}}', ['name' => 'Cisco'])->execute();
        }

        /** Check if device exists */
        if ($this->recordExists('{{%device}}', ['vendor'=> 'Cisco', 'model' => 'c_4500'])) {
            throw new \Exception('Device Cisco c_4500 already exists');
        }

        /** Insert new Device Cisco c_4500  */
        $this->command->insert('{{%device}}', [
            'vendor'             => 'Cisco',
            'model'              => 'c_4500',
            'auth_template_name' => 'cisco_auth_with_password'
        ])->execute();

        /** Get newly inserted device id */
        $device = $this->getEntryIdentifier('{{%device}}', ['vendor'=> 'Cisco', 'model' => 'c_4500'], 'id');

        /** Add device attributes */
        $this->command->batchInsert('{{%device_attributes}}', ['device_id', 'sysobject_id', 'hw', 'sys_description'], [
            [$device, '1.3.6.1.4.1.9.1.626', null, 'Cisco IOS Software, Catalyst 4500 L3 Switch Software (cat4500-IPBASEK9-M), Version 12.2(53)SG1, RELEASE SOFTWARE (fc1)Technical Support: http://www.cisco.com/techsupportCopyright (c) 1986-2009 by Cisco Systems, Inc.Compiled Fri 30-Oct-09 14:39 by pr'],
            [$device, '1.3.6.1.4.1.9.1.626', null, 'Cisco IOS Software, Catalyst 4500 L3 Switch Software (cat4500-ENTSERVICESK9-M), Version 15.0(2)SG10, RELEASE SOFTWARE (fc1)Technical Support: http://www.cisco.com/techsupportCopyright (c) 1986-2015 by Cisco Systems, Inc.Compiled Tue 07-Apr-15 09:46'],
        ])->execute();

        return true;

    }

}
