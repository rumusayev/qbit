<?php

/**
 * @package    MVC
 *
 * @copyright  Copyright (C) 2014  Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
class cUpdates extends controller
{

    public function form()
    {
        $versionsCheckServer = Backstage::gi()->update_server_address . 'licenses/qbitVersions';

        $getVersions = $this->getQBitVersion($versionsCheckServer);
        $getVersions = $getVersions['body'];
        $getVersions = str_replace("f", "", $getVersions);
        $getVersions = str_replace("l", "", $getVersions);

        if (isset($_GET['version']) && !empty($_GET['version'])){
            $getVersions = $_GET['version'];
        }
        $siteVersion = $this->siteVersion();
        $siteVersion = $siteVersion['body'];

        $out = '';




        $out .= "<table style='width:100%;'><tr><td style='width:80%;'>";
        if ($getVersions != '') {
            $out .= '<p>' . Translations::gi()->current_version . ': ' . $siteVersion . '</p>';
            $out .= '<p>' . Translations::gi()->check_server_for_upd . '</p>';
            $versionList = explode("\n", $getVersions);
            foreach ($versionList as $aV) {
                $aV = trim($aV);
                if ($aV > $siteVersion) {

                    $serverVersions = $this->getQBitVersion($versionsCheckServer);
                    $serverVersions = $serverVersions['body'];
                    $verType = strpos($serverVersions, $aV);
                    $verType = substr($serverVersions, $verType);
                    $verType = explode("\n", $verType);
                    $verType = $verType[0];

                    if (strpos($verType, 'l') !== false) {
                        $out .= '<form action="' . Backstage::gi()->portal_url . 'updates/checkUpdateLicenseKey" class="form-inline checkLicense" role="form">
                                    <div class="form-group text-danger">
                                        <label for="exampleInputEmail1">v.' . $aV . ' ' . Translations::gi()->upd_avail_with_lic . '</label><br>
                                        <input class="form-control text-center" type="text" name="updateLicenseKey" placeholder="XXXXXXXXXXXXXXXX" maxlength="16">
                                        <input class="form-control text-center" type="hidden" name="updateVersion" value="'.$aV.'" readonly="readonly">
                                        <button type="submit" class="btn btn-default">'. Translations::gi()->check .'</button>
                                      </div>
                                      <div class="ftpResponce"></div>
                                 </form>
                                 <br>';
                    }

                    if (isset($_GET['token'])) {
                        if (file_exists(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip')) {
                            unlink(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip');
                        }


                        $out .= '<p>' . Translations::gi()->new_update_v . $aV . '</p>';
                        $found = true;

                        //Download The File If We Do Not Have It
                        if (!is_file(Backstage::gi()->ROOT_DIR . 'update_files/' . $_GET['token'])) {

                            $out .= '<p>' . Translations::gi()->downloading_new_upd . '</p>';

                            $newUpdate = file_get_contents(Backstage::gi()->update_server_address . 'licenses/getLicensedUpdateFile?token=' . $_GET['token']);

                            if (!is_dir(Backstage::gi()->ROOT_DIR . 'update_files/')) {
                                $oldmask = umask(0);
                                mkdir(Backstage::gi()->ROOT_DIR . 'update_files/', 0777);
                                umask($oldmask);
                            }

                            $dlHandler = fopen(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip', 'w');
                            if (!fwrite($dlHandler, $newUpdate)) {
                                $out .= '<p>' . Translations::gi()->couldnt_save_upd_abort . '</p>';
                                exit();
                            }
                            fclose($dlHandler);
                            $out .= '<p>' . Translations::gi()->upd_downloaded_and_saved . '</p>';
                        } else {
                            $out .= '<p>' . Translations::gi()->upd_already_downloaded . '</p>';
                        }
                    } elseif (strpos($verType,'f') !== false) {
                        if (file_exists(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip')) {
                            unlink(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip');
                        }


                        $out .= '<p>' . Translations::gi()->new_update_v . $aV . '</p>';
                        $found = true;

                        //Download The File If We Do Not Have It
                        if (!is_file(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip')) {

                            $out .= '<p>' . Translations::gi()->downloading_new_upd . '</p>';

                            if ($this->get_http_response_code(Backstage::gi()->update_server_address . 'updatefiles/qbit-' . $aV . '.zip') != "404") {
                                $newUpdate = file_get_contents(Backstage::gi()->update_server_address . 'updatefiles/qbit-' . $aV . '.zip');
                            } else {
                                $newUpdate = '';
                                throw new QException(array('ER-00027'));
                            }

                            if (!is_dir(Backstage::gi()->ROOT_DIR . 'update_files/')) {
                                $oldmask = umask(0);
                                mkdir(Backstage::gi()->ROOT_DIR . 'update_files/', 0777);
                                umask($oldmask);
                            }

                            $dlHandler = fopen(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip', 'w');
                            if (!fwrite($dlHandler, $newUpdate)) {
                                $out .= '<p>' . Translations::gi()->couldnt_save_upd_abort . '</p>';
                                exit();
                            }
                            fclose($dlHandler);
                            $out .= '<p>' . Translations::gi()->upd_downloaded_and_saved . '</p>';
                        } else {
                            $out .= '<p>' . Translations::gi()->upd_already_downloaded . '</p>';
                        }
                    }

                    if (@$_GET['doUpdate'] == true || isset($_GET['token'])) {
                        //Open The File And Do Stuff
                        $zipHandle = zip_open(Backstage::gi()->ROOT_DIR . 'update_files/qbit-' . $aV . '.zip');
                        $out .= '<ul class="text-left text-muted">';
                        while ($aF = zip_read($zipHandle)) {
                            $thisFileName = zip_entry_name($aF);
                            $thisFileDir = dirname($thisFileName);

                            //Continue if its not a file
                            if (substr($thisFileName, -1, 1) == '/') continue;


                            //Make the directory if we need to...
                            if (!is_dir(Backstage::gi()->ROOT_DIR . $thisFileDir)) {
                                mkdir(Backstage::gi()->ROOT_DIR . $thisFileDir);
                                $out .= '<li>' . Translations::gi()->created_directory . ' ' . $thisFileDir . '</li>';
                            }

                            //Overwrite the file
                            if (!is_dir('../' . $thisFileName)) {
                                $out .= '<li>' . $thisFileName . '...........';
                                $contents = zip_entry_read($aF, zip_entry_filesize($aF));
                                $contents = str_replace("\r\n", "\n", $contents);
                                $updateThis = '';

                                //If we need to run commands, then do it.
                                if ($thisFileName == 'upgrade.php') {
                                    $upgradeExec = fopen('upgrade.php', 'w');
                                    fwrite($upgradeExec, $contents);
                                    fclose($upgradeExec);
                                    include('upgrade.php');
                                    unlink('upgrade.php');
                                    $out .= Translations::gi()->executed . '</li>';
                                } elseif ($thisFileName == 'updates.sql') {
                                    $this->data['sqlQuery'] = $contents;
                                    $this->data = Loader::gi()->getModel($this->data);
                                    $out .= "DATABASE UPDATED" . '</li>';
                                } else {
                                    $updateThis = fopen(Backstage::gi()->ROOT_DIR . $thisFileName, 'w');
                                    fwrite($updateThis, $contents);
                                    fclose($updateThis);
                                    unset($contents);
                                    $out .= Translations::gi()->updated . '</li>';
                                }
                            }
                        }
                        $out .= '</ul><hr>';
                        $updated = true;
                    } else {
                        if (isset($_GET['token'])) {
                            $out .= '<p>' . Translations::gi()->updated_to_v . $aV . '. <a class="btn btn-info" href="?doUpdate=true">&raquo; ' . Translations::gi()->install_now . '</a></p>';
                        } elseif (strpos($verType,'f') !== false){
                            $out .= '<p>' . Translations::gi()->updated_to_v . $aV . '. <a class="btn btn-info" href="?doUpdate=true">&raquo; ' . Translations::gi()->install_now . '</a></p>';
                        } else {
                            $out .= '';
                        }

                    }
                    break;
                }
            }

            if (@$updated == 1) {
                $this->writeNewVersion($aV);
                $out .= '<p class="successUpdate">&raquo; <b>qBit</b> ' . Translations::gi()->updated_to_v . $aV . '</p>';
                @$this->deleteDir(Backstage::gi()->ROOT_DIR . 'update_files');
            } else if (@$found != true) $out .= '<p>&raquo; ' . Translations::gi()->no_upd_available . '</p>';


        } else $out .= '<p>' . Translations::gi()->couldnt_find_latest_rel . '</p>';

        $out .= "</td><td style='vertical-align:top;'><div><ul class='text-left'>";
        $feServerVersions = $this->getQBitVersion($versionsCheckServer);
        $feServerVersions = $feServerVersions['body'];

        $installedVersions = $this->siteVersion('all');
        $installedVersions = $installedVersions['body'];

        $allVersions = explode("\r\n", preg_replace("/[lf]/",'',trim($feServerVersions)));

        $instArray = array();
        foreach($installedVersions as $key=>$version){
            array_push($instArray, preg_replace("/[\n\r]/",'',trim($version)));
        }

        foreach($allVersions as $key=>$version){
            if (in_array($version, $instArray)){
                $out .= "<li>".$version." " . Translations::gi()->installed . "</li>";
            } else {
                $out .= "<li class='text-danger'><a href='".Backstage::gi()->portal_url."updates/form/?version=".$version."' class='text-danger'>".$version." " . Translations::gi()->not_installed . "</a></li>";
            }
        }
        $out .= "</ul></div></td>";
        $out .= "<tr></table>";

        $this->data['update'] = $out;
        $this->data['view_name'] = 'updates';
        $this->data['body'] = Loader::gi()->getView($this->data);
        return $this->data;
    }

    private function getQBitVersion($getVersions)
    {

        $this->data['body'] = file_get_contents($getVersions) or die ('ERROR');
        return $this->data;
    }

    // Check QBITs current version
    private function siteVersion($type = 'last')
    {
        $file = Backstage::gi()->CONTROLLERS_DIR . "updates/versions.php";
        $data = file($file);
        if ($type == 'last'){
            $version = $data[count($data) - 1];
        } elseif ($type = 'all'){
            $version = $data;
        }
        $this->data['body'] = $version;
        return $this->data;
    }

    // Write new version in versions file
    private function writeNewVersion($version)
    {
        $versionsFile = fopen(Backstage::gi()->CONTROLLERS_DIR . "updates/versions.php", "a");
        fwrite($versionsFile, "\r\n" . $version);
        fclose($versionsFile);
    }

    private function get_http_response_code($url)
    {

        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    private static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            //throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function checkUpdateLicenseKey()
    {
        $token = base64_encode($_GET['updateLicenseKey']);
        $version = base64_encode($_GET['updateVersion']);

        // Check remote server with token
        $this->data['body']['ftpresponce'] = file_get_contents(Backstage::gi()->update_server_address . 'licenses/licenseCheckUpdates?token=' . $token . '&updateVersion=' . $version);

        if ( $this->data['body']['ftpresponce'] == "-1" ){
            $this->data['body']['ftpresponce'] == "-1";
        } elseif ( strpos($this->data['body']['ftpresponce'], 'http') !== false ){
            $this->data['body']['ftpresponce'] = Backstage::gi()->portal_url . 'updates/form/?token=' . $token;
        } elseif ( $this->data['body']['ftpresponce'] == "1" ){
            $this->data['body']['ftpresponce'] == "1";
        } elseif ( $this->data['body']['ftpresponce'] == "3" ){
            $this->data['body']['ftpresponce'] == "3";
        }

        return $this->data;
    }

}