(function($) {

    $(document).ready(function(){

        //domain update
        $('.update_domain').each(function() {
            $(this).click(function(e) {
                console.log('clicked');
                e.preventDefault();
                var key = $(this).data('key');
                var domain = $(this).siblings('input[type=text]').first().val();
                var licenceID = '';
                
                if (updateDomain(domain, licenceID)) {
                    console.log('success');
                    return false;
                }
                else {
                    return false;
                }
            });
        });

    });


    /**
     * Update domain ajax
     * 
     * @param {let} licenceID
     * @param {string} domain 
     */
    function updateDomain(licenceID, domain) {

        var formdata = new FormData();
        formdata.append('action', 'updatedomain');
        formdata.append('domain', domain);
        formdata.append('licenceID', licenceID);
        formdata.append('security', gnar_vars.security);

        $.ajax({
            type: 'POST',
            url: gnar_vars.ajax_url,
            contentType: false,
            processData: false,
            data: formdata,
            success: function(data) {
                var resp = JSON.parse(data);
                console.log(data);

                if (resp.status == 'success') {
                    return true;
                }
                
                return false;
            },
            error: function(data) {
                console.log('error' . data);
                return false;
            }
        });
    }


})(jQuery, gnar_vars)