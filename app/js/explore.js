$(document).ready(function () {

    $(".app-icon").click(function () {
       location.href = BASE_URL;
    });

    $.ajax({
        url: BASE_URL + "data/query.php?version=v3&mode=release&p1=getrandom&p2=12",
        type: "GET",
        contentType: "application/json",
        success: function (result) {
            for (let i = 0; i < result.length; i++) {
                let html = '<div class="col-6">' +
                    '<a href="" class="place_link">' +
                    '<div class="card-container">' +
                    '<div class="place_image">' +
                    '<div class="place_name_cont">' +
                    '<div class="place_name"></div>' +
                    '<div class="place_area"></div>' +
                    '</div>' +
                    '</div>' +
                    '</div></a>' +
                    '</div>';
                $('#random-places').append(html);
            }
            for (let i = 0; i < result.length; i++) {
                let el = $('#random-places').find('.place_image').eq(i)
                if (result[i].image !== "") {
                    el.css("background-image", "url(" + BASE_URL + "data/image.php?file=" + result[i].image + 
                    '&w=' + el.width() + '&h=' + el.height() + ")");
                }
                let name = result[i].name;
                let area = result[i].area;
                let region = result[i].region;
                if (get_lang() === "en") {
                    if (result[i].name_en !== ""){
                        name = result[i].name_en;
                    }
                    if (result[i].area_en !== ""){
                        area = result[i].area_en;
                    }
                    if (result[i].region_en !== ""){
                        region = result[i].region_en;
                    }
                }
                $('#random-places').find('.place_name').eq(i).html(name);
                $('#random-places').find('.place_area').eq(i).html(area + ", " + region);
                let url = window.location.href.substr(0, window.location.href.lastIndexOf('/', window.location.href.lastIndexOf('/') - 1));
                $('#random-places').find('.place_link').eq(i).attr('href', url + '/' + result[i].id_area + '/' + result[i].id_string);
            }
        },
        error: function (error) {
            console.log(error);
            alert("Si è verificato un errore!");
        }
    });

});