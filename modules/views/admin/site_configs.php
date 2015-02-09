<?php
require_once('header.php');
echo '<h1>Site Configurations</h1>';
echo '<hr/>';
?>

    <div class="container">
        <div class="row statusRow text-center">

        </div>
    </div>

    <div class="container configData">
        <div class="row">

            <form method="POST" id="changeConfigForm">

            <div class="col-lg-12 col-xs-12 table-responsive portalData">
                <form method="POST" id="installForm">
                    <table class="table">

                        <tr>
                            <td class="active">Portal E-mail</td>
                            <td>
                                <input type="email" class="form-control" id="portal_email" name="portal_email"
                                       value="<?php echo Backstage::gi()->portal_email ?>">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal name</td>
                            <td>
                                <input type="text" class="form-control" id="portal_name" name="portal_name"
                                       value="<?php echo Backstage::gi()->portal_name ?>">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal url</td>
                            <td>
                                <input type="text" class="form-control" id="portal_url" name="portal_url"
                                       value="<?php echo Backstage::gi()->portal_url ?>">
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal languages</td>
                            <td>
                                <?php
                                $portal_langs = explode(',',Backstage::gi()->portal_langs);
                                foreach ($languages as $lang){

                                    $checked = in_array($lang->short, $portal_langs) ? 'checked="checked"' : '';

                                    echo '<div class="checkbox">
                                                    <label>
                                                        <input class="portal_langs" type="checkbox" value="' . $lang->short . '|' . $lang->language . '"
                                                               name="portal_langs[]" ' . $checked . '>
                                                        ' .$lang->language . ' (<b class="langAbbr">' . $lang->short . '</b>)' . '
                                                    </label>
                                                </div>';
                                }
                                ?>
                                <div class="newPortalLangs"></div>
                                <a class="addNewPortalLangs" href="#">+</a>
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Portal default language</td>
                            <td>
                                <?php
                                foreach ($languages as $lang){

                                    $checked = Backstage::gi()->portal_default_lang==$lang->short ? 'checked="checked"' : '';

                                    echo '<div class="radio">
                                                    <label>
                                                        <input class="portal_default_lang" type="radio" name="portal_default_lang"
                                                               value="' . $lang->short . '|' . $lang->language . '" ' . $checked . '>
                                                        ' . $lang->language . '
                                                    </label>
                                                </div>';
                                }
                                ?>
                                <div class="newPortalDefaultLang"></div>
                            </td>
                        </tr>

                        <tr>
                            <td class="active">Template</td>
                            <td>
                                <?php

                                $dir = Backstage::gi()->TEMPLATES_DIR;
                                $skip = array('.', '..', 'admin');
                                $files = scandir($dir);
                                foreach($files as $file) {
                                    if(!in_array($file, $skip)){

                                        $checked = Backstage::gi()->template_name==$file ? 'checked="checked"' : '';
                                        echo '<div class="radio">
                                                <label>
                                                    <input class="template_name" type="radio" value="' . $file . '" name="template_name" ' . $checked . '>
                                                    ' . $file . '
                                                    </label>
                                            </div>';
                                    }

                                }
                                ?>
                            </td>
                        </tr>

                    </table>
            </div>

            <div class="col-lg-12 col-xs-12 table-responsive databaseData">
                <table class="table">

                    <tr>
                        <td class="active">Database Host</td>
                        <td>
                            <input type="text" class="form-control" id="db_host" name="db_host"
                                   value="<?php echo Backstage::gi()->db_host ?>">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database Name</td>
                        <td>
                            <input type="text" class="form-control" id="db_name" name="db_name"
                                   value="<?php echo Backstage::gi()->db_name ?>">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database User</td>
                        <td>
                            <input type="text" class="form-control" id="db_user" name="db_user"
                                   value="<?php echo Backstage::gi()->db_user ?>">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database Password</td>
                        <td>
                            <input type="password" class="form-control" id="db_pass" name="db_pass"
                                   value="<?php echo Backstage::gi()->db_pass ?>">
                        </td>
                    </tr>

                    <tr>
                        <td class="active">Database Tables prefix</td>
                        <td>
                            <input type="text" class="form-control" id="db_table_prefix" name="db_table_prefix"
                                   value="<?php echo Backstage::gi()->db_table_prefix ?>">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="text-center active">
                            <button type="submit" class="btn btn-primary savePortalData">Save</button>
                        </td>
                    </tr>

                </table>
            </div>

            </form>

        </div>

    </div>

<?
require_once('footer.php');
?>