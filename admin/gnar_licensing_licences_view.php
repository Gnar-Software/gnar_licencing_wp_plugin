<?php

class gnar_licensing_licences_view {

    /**
     * Manage licences view
     */
    public static function gnarLicensingLicencesView() {

        $licences = gnar_licence::getAllLicences();

        if (!empty($licences->error)) {
            echo '<span class="gnar_error">' . $licences->error . '</span>';
            return;
        }

        ?>
        <div class="outercont wrap">
            <div class="innercont">
                <div class="titlecont">
                    <h3>Gnar Licensing - Manage Licences</h3>
                </div>
                <form method="post" action="" id="gnar_licensing_manage_form" enctype="multipart/form-data">

                    <?php
                    if (empty($licences)) {
                        echo '<span class="gnar_error">We could not find any licences associated with your api key.</span>';
                        return;
                    }
                    
                    ?>

                    <table class="form-table gnar_manage_licences wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr class="gnar_manage_headers">
                                <th class="manage-column column-title" style="width: 40px">ID</th>
                                <th class="manage-column column-title">Licence Key</th>
                                <th class="manage-column column-title">Domain</th>
                                <th class="manage-column column-title">Customer Email</th>
                                <th class="manage-column column-title">Software ID</th>
                                <th class="manage-column column-title">Licence status</th>
                                <th class="manage-column column-title">Order ID</th>
                                <th class="manage-column column-title">Created</th>
                                <th class="manage-column column-title">Last Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                                foreach ($licences as $licence) {
                                ?>
                                <tr class="gnar_manage_licence">
                                    <td>
                                    <?= $licence->licenceID; ?>
                                    </td>

                                    <td>
                                    <?= $licence->licenceKey; ?>
                                    </td>

                                    <td>
                                    <?= $licence->domain; ?>
                                    </td>

                                    <td>
                                    <?= $licence->customerEmail; ?>
                                    </td>

                                    <td>
                                    <?= $licence->softwareID; ?>
                                    </td>

                                    <td>
                                    <?= $licence->status; ?>
                                    </td>

                                    <td>
                                    <?= $licence->orderID; ?>
                                    </td>

                                    <td>
                                    <?= $licence->createdDate; ?>
                                    </td>

                                    <td>
                                    <?= $licence->lastVerificationDate; ?>
                                    </td>
                                </tr>
                                <?php
                                }

                            ?>
                        </tbody>
                    </table>

                </form>
            </div>
        </div>
        <?php
    }


}

?>