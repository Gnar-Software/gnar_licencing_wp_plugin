(function($) {

    $(document).ready(function(){

        //domain update
        $('.update_domain').each(function() {
            $(this).click(function(e) {
                e.preventDefault();
                var key = $(this).data('key');
                var domain = $(this).sibling('input[type=text]').first().val();
               
                if (updateDomain(domain)) {
                    return true;
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
     * @param {string} domain
     */
    function updateDomain(domain) {
        var formData = new FormData();
        formData.append('action', 'updateDomain');
        formData.append('domain', domain);

        $.ajax({
            type: 'POST',
            url: gnar_vars.ajax_url,
            contentType: false,
            processData: false,
            data: formData,
            success: function(data) {
                return true;
            },
            error: function(data) {
                return false;
            }
        });
    }


})(jQuery, gnar_vars)