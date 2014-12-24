<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class mLicenses extends model
{
    public function licenseCheckUpdates()
    {

        $table_upd_lic = Backstage::gi()->db_table_prefix . 'update_licenses';
        $table_upd_file = Backstage::gi()->db_table_prefix . 'update_files f';
        $table_upd_lic_check = Backstage::gi()->db_table_prefix . 'update_license_checks';


        // Check update license logs
        $this->data['upd_lic_logs'] = $this->dbmanager->tables($table_upd_lic_check)
            ->fields('*')
            ->where("license_key='" . $this->data['updateLicense']['license_key'] . "' AND status!=1")
            ->order("id DESC LIMIT 1")
            ->select();


        $timeLastLog = @strtotime($this->data['upd_lic_logs'][0]->check_datetime);
        $timeNow = @strtotime(date('Y-m-d H:i:s'));

        if ($timeNow - $timeLastLog <= 3) {
            $this->data['license_check'] = '-';
        } else {

            // Check count of usage
            $this->data['licenseUsageCnt'] = $this->dbmanager->tables($table_upd_lic_check)
                ->fields('count(*) cnt')
                ->where("license_key='" . $this->data['updateLicense']['license_key'] . "' AND status=1")
                ->select();

            if ($this->data['licenseUsageCnt'][0]->cnt < 1) {
                // Check license in licenses table
                $this->data['license_check'] = $this->dbmanager->tables($table_upd_lic . " u LEFT JOIN " . $table_upd_file . ' ON u.file_id=f.id AND file_name like "%qbit-'.$this->data['updateLicense']['upsate_version'].'%"')
                    ->fields('*')
                    ->where("u.license_key='" . $this->data['updateLicense']['license_key'] . "' AND is_active=1 AND ('".$this->data['updateLicense']['check_datetime']."' BETWEEN activation_datetime AND deactivation_datetime)")
                    ->select();


                $table_upd_lic_values = array('status'=>1);
                $this->dbmanager->tables($table_upd_lic)
                    ->values($table_upd_lic_values)
                    ->where("license_key='" . $this->data['updateLicense']['license_key'] . "'")
                    ->update();

                if (count($this->data['license_check']) == 0) {
                    $this->data['updateLicense']['status'] = 0;
                    $this->dbmanager->tables($table_upd_lic_check)
                        ->values($this->data['updateLicense'])
                        ->insert();
                } else {
                    if (!empty($this->data['license_check'][0]->file_name)){
                        $this->data['updateLicense']['status'] = 1;
                    } else {
                        $this->data['updateLicense']['status'] = 4;
                    }
                    $this->dbmanager->tables($table_upd_lic_check)
                        ->values($this->data['updateLicense'])
                        ->insert();
                }
            } else {
                $this->data['license_check'] = '3';
                $this->data['updateLicense']['status'] = 3;
                $this->dbmanager->tables($table_upd_lic_check)
                    ->values($this->data['updateLicense'])
                    ->insert();
            }


        }

        return $this->data;
    }

    public function getLicensedUpdateFile(){

        $table_upd_lic = Backstage::gi()->db_table_prefix . 'update_licenses';
        $table_upd_file = Backstage::gi()->db_table_prefix . 'update_files';

        // Check activity for download of file with license key
        $this->data['upd_lic'] = $this->dbmanager->tables($table_upd_lic . " u, " . $table_upd_file . " f")
            ->fields('*')
            ->where("u.license_key='" . $this->data['updateLicense']['license_key'] . "' AND u.is_active=1 AND f.id=u.file_id AND ('".$this->data['updateLicense']['check_datetime']."' BETWEEN activation_datetime AND deactivation_datetime)")
            ->select();

        if (isset($this->data['upd_lic'][0]) && $this->data['upd_lic'][0]->status==1){
            $this->data['file_name'] = $this->data['upd_lic'][0]->file_name;

            // Set status USED
            $table_upd_lic_values = array('status'=>2);
            $this->dbmanager->tables($table_upd_lic)
                ->values($table_upd_lic_values)
                ->where("license_key='" . $this->data['updateLicense']['license_key'] . "'")
                ->update();
        }

        return $this->data;
    }

    public function qbitVersions(){

        // Get qbit versions
        $versions = Backstage::gi()->db_table_prefix . 'qbit_versions';

        $this->data['versions'] = $this->dbmanager->tables($versions)
            ->fields('*')
            ->select();


        return $this->data;
    }
}