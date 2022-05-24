<?php


class gnar_licensing_options_view {


    /**
     * Options page view
     */
    public static function gnarLicensingOptionsView() {

        if (isset($_POST['submit'])) {
            gnar_licensing_options_view::gnarAdminOptionsSave();
        }
    
        ?>
    
        <div class="outercont wrap">
            <div class="innercont">
                <div class="titlecont">
                    <h3>Gnar Licensing - Options</h3>
                </div>
                <form method="post" action="" id="gnar_licensing_options_form" enctype="multipart/form-data">

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label>Gnar Licensing API key:</label></th>
                                <td><input type="password" name="gnar_options_api_key" value="<?= get_option('gnar_licensing_api_key'); ?>"/></td>
                            </tr>
                        </tbody>
                    </table>

                    <input type="submit" name="submit" id="gnar_licensing_options_submit" class="button action" value="Save changes">
                </form>
            </div>
        </div>

        <?php
    }


    /**
     * Options form save
     */
    public static function gnarAdminOptionsSave() {

        if (isset($_POST['gnar_options_api_key'])) {
            $gnarApiKey = esc_attr($_POST['gnar_options_api_key']);
            update_option('gnar_licensing_api_key', $gnarApiKey);
        }

    }

}


?>