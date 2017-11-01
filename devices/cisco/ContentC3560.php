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
class ContentC3560 extends ContentInstaller
{

    public function install()
    {

        /** Insert auth template */
        if (!$this->recordExists('{{%device_auth_template}}', ['name' => 'cisco_with_password'])) {
            $this->command->insert('{{%device_auth_template}}', [
                'name'          => 'cisco_with_password',
                'auth_sequence' => "in:\n{{telnet_login}}\nord:\n{{telnet_password}}\n>\nena\nord:\n{{enable_password}}\n#",
                'description'   => 'Cisco authentication sequence with enable password'
            ])->execute();
        }

        /** Check if vendor exists */
        if (!$this->recordExists('{{%vendor}}', ['name'=> 'Cisco'])) {
            $this->command->insert('{{%vendor}}', ['name' => 'Cisco'])->execute();
        }

        /** Check if device exists */
        if ($this->recordExists('{{%device}}', ['vendor'=> 'Cisco', 'model' => 'c_3560'])) {
            throw new \Exception('Device Cisco c_3560 already exists');
        }

        /** Insert new Device Cisco C3560  */
        $this->command->insert('{{%device}}', [
            'vendor'             => 'Cisco',
            'model'              => 'c_3560',
            'auth_template_name' => 'cisco_with_password'
        ])->execute();

        /** Get newly inserted device id */
        $device = $this->getEntryIdentifier('{{%device}}', ['vendor'=> 'Cisco', 'model' => 'c_3560'], 'id');

        /** Add device attributes */
        $this->command->batchInsert('{{%device_attributes}}', ['device_id', 'sysobject_id', 'hw', 'sys_description'], [
            [$device, '1.3.6.1.4.1.9.1.633', null, 'Cisco IOS Software, C3560 Software (C3560-IPSERVICESK9-M), Version 12.2(53)SE, RELEASE SOFTWARE (fc2)Technical Support: http://www.cisco.com/techsupportCopyright (c) 1986-2009 by Cisco Systems, Inc.Compiled Sun 13-Dec-09 15:45 by prod_rel_team'],
            [$device, '1.3.6.1.4.1.9.1.633', null, 'Cisco IOS Software, C3560 Software (C3560-IPBASEK9-M), Version 12.2(55)SE3, RELEASE SOFTWARE (fc1)Technical Support: http://www.cisco.com/techsupportCopyright (c) 1986-2011 by Cisco Systems, Inc.Compiled Thu 05-May-11 16:13 by prod_rel_team'],
        ])->execute();

        return true;

    }

}
