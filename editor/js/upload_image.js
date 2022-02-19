$(document).ready( function() {
    $(document).on('change', '.btn-file :file', function() {
        let input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {
        let input = $(this).parents('.input-group').find(':text'),
            log = label;
        if( input.length ) {
            input.val(log);
        } else {
            if( log ) alert(log);
        }
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let image = new Image();
                $('#img-upload').css("background-image", "url(" + e.target.result + ")");
                image.src = e.target.result;
                image.onload = function () {
                    $("#img-info").html(this.width + " x " + this.height + " px<br/>" +
                        (input.files[0].size / 1024).toFixed(0) + " KB");
                };
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp").change(function(){
        readURL(this);
    });
});