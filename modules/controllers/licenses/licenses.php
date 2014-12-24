<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cLicenses extends controller
{

    public function licenseCheckUpdates()
    {
        $this->data['updateLicense']['id'] = 0;
        $this->data['updateLicense']['license_key'] = base64_decode($this->data['request']->parameters['token']);
        $this->data['updateLicense']['upsate_version'] = base64_decode($this->data['request']->parameters['updateVersion']);
        $this->data['updateLicense']['check_datetime'] = date('Y-m-d H:i:s');

        $this->data = Loader::gi()->getModel($this->data);

        if ($this->data['license_check'] == '-') {
            echo -1;
        } elseif (is_array($this->data['license_check']) && !empty($this->data['license_check']) && $this->data['license_check'][0]->license_key == $this->data['updateLicense']['license_key'] && !empty($this->data['license_check'][0]->file_name)) {
            echo Backstage::gi()->portal_url . "licenses/licenseCheckUpdates?token=" . $this->data['request']->parameters['token'];
        } elseif(is_array($this->data['license_check']) && !empty($this->data['license_check']) && empty($this->data['license_check'][0]->file_name)){
            echo 4;
        } elseif (is_array($this->data['license_check']) && empty($this->data['license_check'])) {
            echo 0;
        } elseif ($this->data['license_check'] == '3') {
            echo 3;
        }
        $this->data['body'] = '';
        return $this->data;
    }

    public function getLicensedUpdateFile()
    {

        $this->data['updateLicense']['license_key'] = base64_decode($this->data['request']->parameters['token']);
        $this->data['updateLicense']['check_datetime'] = date('Y-m-d H:i:s');

        $this->data = Loader::gi()->getModel($this->data);

        if (isset($this->data['file_name']) && !empty($this->data['file_name'])) {
            $fileContent = file_get_contents(Backstage::gi()->ROOT_DIR . "updatefiles/licensed/" . $this->data['file_name']);

        } else {
            $fileContent = '';
        }

        $this->data['body'] = $fileContent;
        return $this->data;
    }

    public function qbitVersions(){
        $this->data = Loader::gi()->getModel($this->data);

        foreach($this->data['versions'] as $key=>$version){
            echo $version->version . $version->type . "\r\n";
        }
        $this->data['body'] = '';
        return $this->data;
    }

}