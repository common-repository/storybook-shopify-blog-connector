

function SBC_EBKService() {
    this.ajaxCall = function(data, cb) {
		jQuery.post(window.ajaxurl, data, function(res) {
            var response = {};
            try {
                response = JSON.parse(res);
            } catch (err) {
                return alert(err);
            }
            if (response.status === 'OK' && cb) {
                cb(response.data);
            } else {
                alert(response.message);
            }
		});
    }
}

var storyBookService = new SBC_EBKService();

jQuery( document ).ready(function() {

    jQuery('#connect_btn').click(function(e) {
        e.preventDefault();
        if (jQuery('#accessKey').val() === '') {
            return alert('Please provide the access key first');
        }        
        storyBookService.ajaxCall({
            'action': 'update_access_key',
            'accessKey': jQuery('#accessKey').val(),
        }, function(data) {
            window.location.reload();
        });        
    })
});