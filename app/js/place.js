$(document).ready(function () {

    $(".app-icon").click(function () {
        location.href = BASE_URL;
    });

    let photo_filename = $('#photo_filename').text().trim()
    let photo_url = BASE_URL + "data/image.php?mode=release&file=" + photo_filename + 
        '&w=' + $('#place_image').width() + '&h=' + $('#place_image').height()
    console.log($('#place_image').width(), $('#place_image').height())
    $('#place_image').css('background-image', 'url(' + photo_url + ')')

    $.ajax({
        url: BASE_URL + "data/query.php?version=v3&mode=release&p1=getrandom&p2=24",
        type: "GET",
        contentType: "application/json",
        success: function (result) {
            console.log(result);
            for (let i = 0; i < result.length; i++) {
                let html = '<div class="col-lg-2 col-md-4 col-sm-6 col-12">' +
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
                    el.css("background-image", "url(" + BASE_URL + "data/image.php?mode=release&file=" + result[i].image + 
                    '&w=' + el.width() + '&h=' + el.height() + ")")
                }
                let name = result[i].name;
                if (get_lang() === "en" && result[i].name_en !== ""){
                    name = result[i].name_en;
                }
                let area = result[i].area;
                if (get_lang() === "en" && result[i].area_en !== ""){
                    area = result[i].area_en;
                }
                let region = result[i].region;
                if (get_lang() === "en" && result[i].region_en !== ""){
                    region = result[i].region_en;
                }
                $('#random-places').find('.place_name').eq(i).html(name);
                $('#random-places').find('.place_area').eq(i).html(area + ", " + region);
                let id_area = result[i].id_area;
                if (get_lang() === "en"){
                    id_area = result[i].id_area_en;
                }
                let id_string = result[i].id_string;
                if (get_lang() === "en"){
                    id_string = result[i].id_string_en;
                }
                $('#random-places').find('.place_link').eq(i).attr('href', BASE_URL + '/' + id_area + '/' + id_string);
                if (get_lang() === "en"){
                    $('#random-places').find('.place_link').eq(i).attr('href', BASE_URL + 'en/' + id_area + '/' + id_string);
                }
            }
        },
        error: function (error) {
            console.log(error);
            alert("Si è verificato un errore!");
        }
    });

});