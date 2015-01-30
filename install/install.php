<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rumusayev
 * Date: 11/14/14
 * Time: 3:06 PM
 * To change this template use File | Settings | File Templates.
 */

error_reporting(0);

if (!empty($_POST['portal_url']) && !empty($_POST['db_host']) && !empty($_POST['db_name']) && !empty($_POST['db_user']) && !empty($_POST['db_pass'])) {


    $emptyConfig = file_get_contents("config.php-dist");


    $emptyConfig = str_replace('"db_host" => ""', '"db_host" => "' . $_POST['db_host'] . '"', $emptyConfig);
    $emptyConfig = str_replace('"db_name" => ""', '"db_name" => "' . $_POST['db_name'] . '"', $emptyConfig);
    $emptyConfig = str_replace('"db_user" => ""', '"db_user" => "' . $_POST['db_user'] . '"', $emptyConfig);
    $emptyConfig = str_replace('"db_pass" => ""', '"db_pass" => "' . $_POST['db_pass'] . '"', $emptyConfig);

    if (isset($_POST['db_table_prefix']) && !empty($_POST['db_table_prefix'])) {

        if (substr($_POST['db_table_prefix'], -1) == "_") {
            $_POST['db_table_prefix'] = rtrim($_POST['db_table_prefix'], "_");
        }
        $_POST['db_table_prefix'] = $_POST['db_table_prefix'] . "_";
    } else {
        $emptyConfig = str_replace('"db_table_prefix" => ""', '"db_table_prefix" => ""', $emptyConfig);
    }
    $emptyConfig = str_replace('"db_table_prefix" => ""', '"db_table_prefix" => "' . $_POST['db_table_prefix'] . '"', $emptyConfig);

    $emptyConfig = str_replace('"portal_email" => ""', '"portal_email" => "' . $_POST['portal_email'] . '"', $emptyConfig);
    $emptyConfig = str_replace('"portal_name" => ""', '"portal_name" => "' . $_POST['portal_name'] . '"', $emptyConfig);
    $emptyConfig = str_replace('"portal_url" => ""', '"portal_url" => "' . $_POST['portal_url'] . '"', $emptyConfig);
    $emptyConfig = str_replace('"template_name" => ""', '"template_name" => "' . $_POST['template_name'] . '"', $emptyConfig);
    $default_lang_parts = explode("|", $_POST['portal_default_lang']);
    $emptyConfig = str_replace('"portal_default_lang" => ""', '"portal_default_lang" => "' . $default_lang_parts[0] . '"', $emptyConfig);

    if (!empty($_POST['portal_langs'])) {
        $langsShortArray = array();
        foreach ($_POST['portal_langs'] as $lang) {
            $lang = explode('|', $lang);
            $langsShortArray[] = $lang[0];
        }

        $emptyConfig = str_replace('"portal_langs" => ""', '"portal_langs" => "' . implode(",", $langsShortArray) . '"', $emptyConfig);
    } else {
        $default_lang_parts = explode("|", $_POST['portal_default_lang']);
        $emptyConfig = str_replace('"portal_langs" => ""', '"portal_langs" => "' . $default_lang_parts[0] . '"', $emptyConfig);
    }

    $fname = "../config/config.php";

    $f = fopen($fname, "w") or die("Unable to open file!");
    fwrite($f, $emptyConfig);
    fclose($f);


    $connect_str = 'mysql:host=' . $_POST['db_host'] . ';port=3306;dbname=' . $_POST['db_name'] . ';charset=utf8';


    try {
        $db = new PDO($connect_str, $_POST['db_user'], $_POST['db_pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $e) {

        if ($e->getCode() == '2002') {
            $status = "DB Error: Unknown host";
        } elseif ($e->getCode() == '1049') {
            $status = "DB Error: Unknown database";
        } elseif ($e->getCode() == '1045') {
            $status = "DB Error: User error";
        } elseif ($e->getCode() == '1045') {
            $status = "DB Error: User error";
        }
        echo '{"status":"' . $status . '"}';

        die();
    }

    $sql = file_get_contents('../qbit3-clean.sql');

    $sql = str_replace("DROP TABLE IF EXISTS `", "DROP TABLE IF EXISTS `" . @$_POST['db_table_prefix'] . "", $sql);
    $sql = str_replace("CREATE TABLE `", "CREATE TABLE `" . @$_POST['db_table_prefix'] . "", $sql);

    $sql = str_replace("INSERT INTO `", "INSERT INTO `" . @$_POST['db_table_prefix'] . "", $sql);

    $qr = $db->exec($sql);


    if (!empty($_POST['portal_langs'])) {
        foreach ($_POST['portal_langs'] as $langLong) {

            $langLong = explode('|', $langLong);
            $sql = "INSERT INTO `" . @$_POST['db_table_prefix'] . "languages` VALUES (0, '" . $langLong[0] . "', '" . $langLong[1] . "');";
            $qr = $db->exec($sql);
        }
    } else {
        $langLong = explode('|', $_POST['portal_default_lang']);
        $sql = "INSERT INTO `" . @$_POST['db_table_prefix'] . "languages` VALUES (0, '" . $langLong[0] . "', '" . $langLong[1] . "');";
        $qr = $db->exec($sql);
    }

    $sql = "INSERT INTO `" . @$_POST['db_table_prefix'] . "users` VALUES ('3', '" . $_POST['admin_login'] . "', '" . md5($_POST['admin_password']) . "', '', '', '', '" . $_POST['admin_email'] . "', '', '1');";
    $qr = $db->exec($sql);

    // Add DEMO DATA
    if (!empty($_POST['add_demo_data']) && $_POST['add_demo_data']==1) {
        // Unzip materials
        $zip = new ZipArchive;
        $res = $zip->open('data/' . $_POST['template_name'] . '/materials/catalogs.zip');
        if ($res === TRUE) {
            $zip->extractTo('../materials/catalogs/');
            $zip->close();
        } else {

        }

        $dataStructure = file_get_contents("data/structure.html");
        $dataStructure = str_replace("INSERT INTO `", "INSERT INTO `" . @$_POST['db_table_prefix'] . "", $dataStructure);
        $db->exec($dataStructure);

        foreach ($_POST['portal_langs'] as $langData) {
            $langsDatas = explode('|', $langData);
            $demoData = file_get_contents("data/" . $_POST['template_name'] . "/" . $langsDatas[0] . ".html");
            $demoData = str_replace("INSERT INTO `", "INSERT INTO `" . @$_POST['db_table_prefix'] . "", $demoData);
            $demoData = str_replace("SELECT id FROM `", "SELECT id FROM `" . @$_POST['db_table_prefix'] . "", $demoData);
            $demoData = str_replace("translations_words'", @$_POST['db_table_prefix'] . "translations_words'", $demoData);
            $db->exec($demoData);

        }

    }

    $static_translations_add = file_get_contents("translations/translations_structure.html");
    $static_translations_add = str_replace("INSERT INTO `", "INSERT INTO `" . @$_POST['db_table_prefix'] . "", $static_translations_add);
    $qr = $db->exec($static_translations_add);

    foreach ($_POST['portal_langs'] as $langLong) {
        $langLong = explode('|', $langLong);
        $static_translations = file_get_contents("translations/" . $langLong[0] . ".html");
        $static_translations = str_replace("ALTER TABLE `", "ALTER TABLE `" . @$_POST['db_table_prefix'] . "", $static_translations);
        $static_translations = str_replace("UPDATE `", "UPDATE `" . @$_POST['db_table_prefix'] . "", $static_translations);
        $static_translations = str_replace("translations_words'", @$_POST['db_table_prefix'] . "translations_words'", $static_translations);
        $qr = $db->exec($static_translations);

    }



    $status = "Done";

    echo '{"status":"' . $status . '"}';
}