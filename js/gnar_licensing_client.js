(function($) {

    $(document).ready(function() {

        //domain update
        $('.update_domain').each(function() {
            $(this).click(function(e) {
                console.log('clicked');
                e.preventDefault();
                var licenceID = $(this).data('licenceid');
                var domain = $(this).siblings('input[type=text]').first().val();
                var parentDiv = $(this).parent();

                updateDomain(licenceID, domain, parentDiv);

            });
        });

    });


    /**
     * Update domain ajax
     * 
     * @param {let} licenceID
     * @param {string} domain 
     */
    function updateDomain(licenceID, domain, parentDiv) {

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
                console.log(resp.status);

                if (resp.status == 'success') {
                    $(parentDiv).append('<span class="successtick">✔</span>');
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