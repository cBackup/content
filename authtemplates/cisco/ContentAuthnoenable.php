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

namespace app\modules\cds\content\authtemplates\cisco;

use app\modules\cds\components\ContentInstaller;

/**
 * @package app\modules\cds\content\authtemplates\cisco
 */
class ContentAuthnoenable extends ContentInstaller
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function install()
    {

        /** Check if record exists */
        if ($this->recordExists('{{%device_auth_template}}', ['name' => 'cisco_auth_no_password'])) {
            throw new \Exception('Auth template cisco_auth_no_password already exists');
        }

        $this->command->insert('{{%device_auth_template}}', [
            'name'          => 'cisco_auth_no_password',
            'auth_sequence' => "in:\n{{telnet_login}}\nord:\n{{telnet_password}}\n#",
            'description'   => 'Cisco authentication sequence without enable password'
        ])->execute();

        return true;

    }
}

