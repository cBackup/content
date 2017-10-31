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

namespace app\modules\cds\content\devices\zte;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\devices\zte
 */
class ContentZxponc320 extends ContentInstaller
{
    public function install()
    {

        /** Insert auth template */
        if (!$this->recordExists('{{%device_auth_template}}', ['name' => 'zte_auth'])) {
            $this->command->insert('{{%device_auth_template}}', [
                'name'          => 'zte_auth',
                'auth_sequence' => '#',
                'description'   => 'ZTE authentication sequence'
            ])->execute();
        }

        /** Check if vendor exists */
        if (!$this->recordExists('{{%vendor}}', ['name'=> 'ZTE'])) {
            $this->command->insert('{{%vendor}}', ['name' => 'ZTE'])->execute();
        }

        /** Check if device exists */
        if ($this->recordExists('{{%device}}', ['vendor'=> 'ZTE', 'model' => 'ZXPON_C320'])) {
            throw new \Exception('Device ZTE ZXPON_C320 already exists');
        }

        /** Insert new Device ZTE C2960  */
        $this->command->insert('{{%device}}', [
            'vendor'             => 'ZTE',
            'model'              => 'ZXPON_C320',
            'auth_template_name' => 'zte_auth'
        ])->execute();

        /** Get newly inserted device id */
        $device = $this->getEntryIdentifier('{{%device}}', ['vendor'=> 'ZTE', 'model' => 'ZXPON_C320'], 'id');

        /** Add device attributes */
        $this->command->batchInsert('{{%device_attributes}}', ['device_id', 'sysobject_id', 'hw', 'sys_description'], [
            [$device, '1.3.6.1.4.1.3902.1015.320.1.2', null, 'ZXR10 ROS Version V4.6.02A ZXPON C320 Software, Version V1.2.5P2 Copyright (c) 2000-2006 by ZTE Corporation Compiled 2013-09-23 07:34:16'],
        ])->execute();

        return true;

    }
}
