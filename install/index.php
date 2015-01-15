<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rumusayev
 * Date: 11/14/14
 * Time: 11:33 AM
 * To change this template use File | Settings | File Templates.
 */

error_reporting(E_ALL);
session_start();
ob_start();

header('Pragma: no-cache');
header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Content-Type: text/html');


if (version_compare(phpversion(), '5.1.0', '<') == true) {
    die ('Please use PHP 5.1 or higher.');
}


define('DR', '/');
define('PARENT_DR', '../');
// Root constants and directories
define('ROOT_DIR', ".." . DR);
define('LIBS_DIR', ROOT_DIR . 'libs' . DR);
define('CONFIG_DIR', ROOT_DIR . 'config' . DR);
define('LANGS_DIR', CONFIG_DIR . 'languages' . DR);
define('MODULES_DIR', ROOT_DIR . 'modules' . DR);
define('MATERIALS_DIR', ROOT_DIR . 'materials' . DR);
define('TEMPLATES_DIR', ROOT_DIR . 'templates' . DR);
define('EXTERNAL_DIR', ROOT_DIR . 'external' . DR);
define('CGI_DIR', ROOT_DIR . 'cgi' . DR);


// Autoloading classes
function __autoload($class_name)
{
    $filename = strtolower($class_name) . '.php';
    $libs_file = LIBS_DIR . $filename;
    $config_file = CONFIG_DIR . $filename;
    if (!file_exists($libs_file))
        if (!file_exists($config_file))
            return false;
        else
            include($config_file);
    else
        include($libs_file);
}

if (Backstage::gi()->portal_installed == 1) {
    header("Location: " . Backstage::gi()->portal_url);
}

$languagesInstall = array(
   "az" => "Azərbaycan",
   "en" => "English",
   "ru" => "Русский",
);

require_once('templates/default/views/header.php');
echo '<h1 class="text-center">Installation</h1>';
echo '<hr/>';
?>

    <div class="container">
        <div class="row statusRow text-center">

        </div>
    </div>

    <div class="container configData">
        <div class="row">

            <div class="col-lg-12 col-xs-12 table-responsive portalData">
                <form method="POST" id="installForm">
                    <table class="table">

                        <tr>
                            <td class="active">Administrator login</td>
                            <td>
                                <input type="text" class="form-control" id="admin_login" name="admin_login"
                                       placeholder="admin">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Administrator password</td>
                            <td>
                                <input type="password" class="form-control" id="admin_password" name="admin_password"
                                       placeholder="password">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Administrator E-mail</td>
                            <td>
                                <input type="email" class="form-control" id="admin_email" name="admin_email"
                                       placeholder="admin@domain.com">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal E-mail</td>
                            <td>
                                <input type="email" class="form-control" id="portal_email" name="portal_email"
                                       placeholder="example@domain.com">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal name</td>
                            <td>
                                <input type="text" class="form-control" id="portal_name" placeholder="Example"
                                       name="portal_name">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal url</td>
                            <td>
                                <input type="text" class="form-control" id="portal_url" name="portal_url"
                                       placeholder="http://example.com/">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal languages</td>
                            <td>
                                <?php

                                foreach ($languagesInstall as $key=>$lang){
                                    echo '<div class="checkbox">
                                                    <label>
                                                        <input class="portal_langs" type="checkbox" value="' . $key . '|' . $lang . '"
                                                               name="portal_langs[]">
                                                        ' .$lang . '
                                                    </label>
                                                </div>';
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal default language</td>
                            <td>
                                <?php
                                foreach ($languagesInstall as $key=>$lang){
                                    echo '<div class="radio">
                                                    <label>
                                                        <input class="portal_default_lang" type="radio" name="portal_default_lang"
                                                               value="' . $key . '|' . $lang . '">
                                                        ' . $lang . '
                                                    </label>
                                                </div>';
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" class="text-center active">
                                <button type="button" class="btn btn-default nextDBconfig">Next >></button>
                            </td>
                        </tr>

                    </table>
            </div>

            <div class="col-lg-12 col-xs-12 table-responsive databaseData" hidden="hidden">
                <table class="table">

                    <tr>
                        <td class="active">Database Host</td>
                        <td>
                            <input type="text" class="form-control" id="db_host" name="db_host"
                                   placeholder="localhost">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database Name</td>
                        <td>
                            <input type="text" class="form-control" id="db_name" name="db_name"
                                   placeholder="websitedb">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database User</td>
                        <td>
                            <input type="text" class="form-control" id="db_user" name="db_user"
                                   placeholder="root">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database Password</td>
                        <td>
                            <input type="password" class="form-control" id="db_pass" name="db_pass"
                                   placeholder="password">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database Tables prefix</td>
                        <td>
                            <input type="text" class="form-control" id="db_table_prefix" name="db_table_prefix"
                                   placeholder="pre">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="text-center active">
                            <button type="button" class="btn btn-default backPortalConfig"><< Back</button>
                            <button type="button" class="btn btn-primary savePortalData">Save</button>
                        </td>
                    </tr>

                </table>
            </div>

            </form>

        </div>

    </div>

    <div class="container savingConfig" hidden="hidden">
        <div class="row">
            <div class="col-lg-12 col-xs-12 text-center">
                <p>Saving...</p>
                <br>
                <img src="templates/default/images/loader.gif">
            </div>
        </div>
    </div>

<?php


require_once('templates/default/views/footer.php');
