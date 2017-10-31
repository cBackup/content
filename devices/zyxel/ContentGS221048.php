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

namespace app\modules\cds\content\devices\zyxel;

use app\modules\cds\components\ContentInstaller;

class ContentGS221048 extends ContentInstaller
{

    public function install()
    {

        /** Insert auth template */
        if (!$this->recordExists('{{%device_auth_template}}', ['name' => 'zyxel_auth'])) {
            $this->command->insert('{{%device_auth_template}}', [
                'name'          => 'zyxel_auth',
                'auth_sequence' => "ame:\n{{telnet_login}}\nord:\n{{telnet_password}}\n#",
                'description'   => 'Zyxel authentication sequence'
            ])->execute();
        }

        /** Check if device exists */
        if ($this->recordExists('{{%device}}', ['vendor'=> 'Zyxel', 'model' => 'GS2210_48'])) {
            throw new \Exception('Device Zyxel GS2210_48 already exists');
        }

        /** Insert new Device Zyxel GS2210_48  */
        $this->command->insert('{{%device}}', [
            'vendor'             => 'Zyxel',
            'model'              => 'GS2210_48',
            'auth_template_name' => 'zyxel_auth'
        ])->execute();

        /** Get newly inserted device id */
        $device = $this->getEntryIdentifier('{{%device}}', ['vendor'=> 'Zyxel', 'model' => 'GS2210_48'], 'id');

        /** Add device attributes */
        $this->command->batchInsert('{{%device_attributes}}', ['device_id', 'sysobject_id', 'hw', 'sys_description'], [
            [$device, '1.3.6.1.4.1.890.1.15', null, 'GS2210-48']
        ])->execute();

        return true;

    }

}
