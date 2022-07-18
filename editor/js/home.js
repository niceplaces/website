$(document).ready(function () {

    const base_url = "https://www.niceplaces.it/";
    //const base_url = "http://localhost/niceplaces/";

    $("#select_place").hide();
    $("#place_panel").hide();
    $("#events_panel").hide();
    $(".description_en").hide();

    loadAreasList();

    function loadAreasList() {
        $.ajax({
            url: base_url + "data/v3/debug/areas",
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                console.log(result);
                $("#area").html('<option selected>Seleziona...</option>');
                $("#places_area_to_add").html('<option selected>Seleziona...</option>');
                for (let i = 0; i < result.length; i++) {
                    $("#area").append("<option value=\"" + result[i].id + "\">" + result[i].name + " (" + result[i].count + ")</option>");
                    $("#places_area_to_add").append("<option value=\"" + result[i].id + "\">" + result[i].name + "</option>");
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    function loadPlacesList(id_area) {
        $.ajax({
            url: base_url + "data/v3/debug/areas/" + id_area,
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                $("#place").html('<option selected>Seleziona...</option>');
                for (let i = 0; i < result.length; i++) {
                    let name = result[i].name;
                    if (result[i].has_image){
                        name += '<img src="../../assets/icons/picture.png">';
                    }
                    $("#place").append("<option value=\"" + result[i].id + "\">" + name + "</option>");
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    let loadedJson = [];
    let editedJson = [];

    function loadPlace(id) {
        $.ajax({
            url: base_url + "data/v3/debug/places/" + id,
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                console.log(result)
                loadedJson = result;
                editedJson = $.extend(true, {}, result);
                let position = {lat: 0, lng: 0};
                if (result.latitude !== "0" && result.longitude !== "0"){
                    position = {lat: parseFloat(result.latitude), lng: parseFloat(result.longitude)};
                }
                placeMarker(position, googleMap);
                googleMap.setCenter(new google.maps.LatLng(position.lat, position.lng));
                $('#english_name').val(result.name_en);
                $('#photo').val(result.image);
                $('#photo_credits').val(result.img_credits);
                $('#selected-photo').css("background-image", "url('../data/photos/debug/" + result.image + "')");
                $('#description').val(result.description);
                $('#description_en').val(result.description_en);
                $('#desc_sources').val(result.desc_sources);
                $('#wiki_url').val(result.wiki_url);
                $('#wiki_url_en').val(result.wiki_url_en);
                $('#facebook').val(result.facebook);
                $('#instagram').val(result.instagram);
                $(".char_counter span").html($(".char_counter").parent().find('textarea').val().length);
                const div_events = $("#div-events");
                const add_event_button = '<div class="row">' +
                    '<div class="col text-center">' +
                    '<button type="button" class="btn btn-outline-primary" id="btn_add_event">Aggiungi evento</button>' +
                    '</div></div>';
                div_events.html(add_event_button);
                $.each(result.events, function (i, val) {
                    div_events.append(newEventRow(val.id, val.date, val.description));
                });
                $("#btn_add_event").on('click', function () {
                    div_events.append(newEventRow("", "", ""));
                    div_events.find(".btn_remove_event").on('click', function () {
                        $(this).parents().eq(1).remove();
                    });
                });
                div_events.find(".btn_remove_event").on('click', function () {
                    $(this).parents().eq(1).remove();
                });
                loadPhotos();
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    $("#area").change(function () {
        const id_area = $("#area").val();
        loadPlacesList(id_area);
        $("#select_place").show();
        clearFields();
    });

    $("#place").change(function () {
        const id = $("#place").val();
        loadPlace(id);
        $("#place_panel").show();
        $("#events_panel").show();
    });

    $("#btn_save").click(function () {
        const id = $("#place").val();
        let lat = $('#lat').html();
        if (lat === ""){
            editedJson.latitude = "0";
        } else {
            editedJson.latitude = lat;
        }
        let lon = $('#lon').html();
        if (lon === ""){
            editedJson.longitude = "0";
        } else {
            editedJson.longitude = lon;
        }
        editedJson.name_en = $("#english_name").val().replace(/'/g, "\\'");;
        editedJson.description = $("#description").val().replace(/'/g, "\\'");
        editedJson.description_en = $("#description_en").val().replace(/'/g, "\\'");
        editedJson.desc_sources = $("#desc_sources").val();
        editedJson.wiki_url = $("#wiki_url").val();
        editedJson.wiki_url_en = $("#wiki_url_en").val();
        editedJson.facebook = $("#facebook").val();
        editedJson.instagram = $("#instagram").val();
        editedJson.image = $("#photo").val();
        editedJson.img_credits = $("#photo_credits").val();
        editedJson.events = [];
        $(".row-event").each(function (i, val) {
            editedJson.events.push({
                "id": $(this).find(".event-id").text(),
                "date": $(this).find(".event-date").val(),
                "description": $(this).find(".event-desc").val().replace(/'/g, "\\'")
            });
        });
        let events_actions = computeDiffs();
        $.ajax({
            url: base_url + "data/v3/debug/places/" + id + "/update",
            type: "POST",
            data: JSON.stringify(editedJson),
            contentType: "application/json",
            dataType: "json",
            success: function (result) {
                console.log(result);
                if (result){
                    $.ajax({
                        url: base_url + "data/v3/debug/places/" + id + "/events/update",
                        type: "POST",
                        data: JSON.stringify(events_actions),
                        contentType: "application/json",
                        dataType: "json",
                        success: function (result) {
                            console.log(result);
                            if (result){
                                alert("Salvato.");
                                insertChangeLog();
                                //location.reload();
                            } else {
                                alert("Si è verificato un errore!");
                            }
                        },
                        error: function (error) {
                            console.log(error);
                            alert("Si è verificato un errore!");
                        }
                    });
                } else {
                    alert("Si è verificato un errore!");
                }
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
    });

    function computeDiffs() {
        console.log(loadedJson, editedJson);
        console.log(JSON.stringify(loadedJson).localeCompare(JSON.stringify(editedJson)));
        let events_actions = {
            "insert": [],
            "update": [],
            "delete": []
        };
        for (let i = 0; i < editedJson.events.length; i++) {
            if (editedJson.events[i].date.localeCompare("") !== 0 && editedJson.events[i].description.localeCompare("") !== 0){
                if (editedJson.events[i].id.localeCompare("") === 0) {
                    events_actions.insert.push(editedJson.events[i]);
                } else {
                    let j = 0;
                    while (j < loadedJson.events.length) {
                        if (loadedJson.events[j].id !== editedJson.events[i].id) {
                            j++;
                        } else {
                            break;
                        }
                    }
                    if (JSON.stringify(loadedJson.events[j]).localeCompare(JSON.stringify(editedJson.events[i])) !== 0) {
                        events_actions.update.push(editedJson.events[i]);
                    }
                }
            }
        }
        for (let i = 0; i < loadedJson.events.length; i++) {
            let j = 0;
            while (j < editedJson.events.length) {
                if (loadedJson.events[i].id !== editedJson.events[j].id) {
                    j++;
                } else {
                    break;
                }
            }
            if (j === editedJson.events.length) {
                events_actions.delete.push(loadedJson.events[i]);
            }
        }
        console.log(events_actions);
        return events_actions;
    }

    function newEventRow(id, date, desc) {
        return '<div class="row row-event">' +
            '<div class="col-sm-2">' +
            '<span class="event-id">' + id + '</span>' +
            '<input type="text" class="form-control event-date" value="' + date + '">' +
            '</div>' +
            '<div class="col-sm-9">' +
            '<input type="text" class="form-control event-desc" value="' + desc + '">' +
            '</div>' +
            '<div class="col-sm-1">' +
            '<button type="button" class="btn btn-outline-danger btn_remove_event">X</button>' +
            '</div>' +
            '</div>';
    }

    $("textarea").on('change keyup paste', function () {
        $(this).parent().find(".char_counter span").html($(this).val().length);
    });

    $("#modal_areas_save").click(function () {
        const modal = $("#modal_areas");
        const name = $("#area_to_add").val().replace(/'/g, "\\'");
        $.ajax({
            url: base_url + "data/query.php?version=v3&mode=debug&p1=insertarea&p2=" + name,
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                console.log(result);
                if (result){
                    alert("Salvato.");
                    loadAreasList();
                    modal.modal("hide");
                } else {
                    alert("Si è verificato un errore!");
                }
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
    });

    $("#modal_places_save").click(function () {
        const modal = $("#modal_places");
        const id = $("#places_area_to_add").val().replace(/'/g, "\\'");
        const name = $("#place_to_add").val().replace(/'/g, "\\'");
        $.ajax({
            url: base_url + "data/query.php?version=v3&mode=debug&p1=insertplace&p2=" + id + "&p3=" + name,
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                console.log(result);
                if (result){
                    alert("Salvato.");
                    $("#area").val(id);
                    loadPlacesList(id);
                    $("#place_panel").show();
                    modal.modal("hide");
                } else {
                    alert("Si è verificato un errore!");
                }
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
    });

    function clearFields(){
        $("textarea").val("");
        $('#latitude').val("");
        $('#longitude').val("");
        $('#photo').val("");
        $('#selected-photo').css("background-image", "");
        $('#char_counter').html("0");
        const add_event_button = '<div class="row">' +
            '<div class="col text-center">' +
            '<button type="button" class="btn btn-outline-primary" id="btn_add_event">Aggiungi evento</button>' +
            '</div></div>';
        $("#div-events").html(add_event_button);
    }

    $("#btn_last_edits").click(function () {
        $.ajax({
            url: base_url + "/data/v3/debug/lastupdated",
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                console.log(result);
                const table = $("#table_updated tbody");
                console.log(table);
                table.empty();
                for (let i = 0; i < result.length; i++){
                    table.append("<tr>" +
                        "<td>" + result[i].place + " (" + result[i].area + ")</td>" +
                        "<td>" + normalizeTimestamp(result[i].time) + "</td>" +
                        "</tr>");
                }
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
        $.ajax({
            url: base_url + "/data/v3/debug/lastinserted",
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                console.log(result);
                const table = $("#table_inserted tbody");
                console.log(table);
                table.empty();
                for (let i = 0; i < result.length; i++){
                    table.append("<tr>" +
                        "<td>" + result[i].place + " (" + result[i].area + ")</td>" +
                        "<td>" + normalizeTimestamp(result[i].time) + "</td>" +
                        "</tr>");
                }
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
    });

    function insertChangeLog() {
        const id_user = $("#id_user").html();
        const id_place = $("#place").val();
        $.ajax({
            url: base_url + "data/query.php?version=v2&mode=debug&p1=insertchange&p2=" + id_user + "&p3=" + id_place,
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                //location.reload();
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
    }

    function loadPhotos() {
        $.ajax({
            url: base_url + "data/debug/photos",
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                $('#modal_photos .photos-body').empty();
                if (result){
                    const items_per_row = 3;
                    for (let i = 0; i < result.length; i += items_per_row){
                        let html = '<div class="row">';
                        for (let j = i; j < i + items_per_row && result[j] !== undefined; j++){
                            html += '<div class="col text-center">' +
                                '<img class="thumb" src="../data/photos/debug/' + result[j] + '"/><br/>' +
                                '<small>' + result[j] + '</small>' +
                                '</div>';
                        }
                        html += '</div>';
                        $('#modal_photos .photos-body').append(html);
                    }
                    $('#searchbar-photos').keyup(function () {
                        let pattern = $(this).val().toLowerCase();
                        $('img.thumb').each(function () {
                            let path = $(this).attr("src");
                            let src = path.substring(path.lastIndexOf('/')+1).toLowerCase();
                            if (src.includes(pattern)){
                                $(this).parent().show();
                            } else {
                                $(this).parent().hide();
                            }
                        });
                    });
                    $('img.thumb').click(function () {
                        let path = $(this).attr("src");
                        let src = path.substring(path.lastIndexOf('/')+1);
                        $('#photo').val(src);
                        $('#selected-photo').css("background-image", "url(" + path + ")");
                        $('#modal_photos').modal("hide");
                    });
                } else {
                    alert("Si è verificato un errore!");
                }
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
    }

    $.ajax({
        url: base_url + "data/v3/debug/stats",
        type: "GET",
        contentType: "application/json",
        success: function (result) {
            if (result){
                $('#modal_stats .modal-body').append(
                    "Luoghi: " + result.places + "<br/>" +
                    "Luoghi con foto: " + result.places_with_photo + " (" + ((result.places_with_photo*100)/result.places).toFixed(0) + "%)<br/>" +
                    "Luoghi con descrizione: " + result.places_with_description + " (" + ((result.places_with_description*100)/result.places).toFixed(0) + "%) <br/>" +
                    "Luoghi con descrizione in inglese: " + result.places_with_description_en + " (" + ((result.places_with_description_en*100)/result.places).toFixed(0) + "%)");
            } else {
                alert("Si è verificato un errore!");
            }
        },
        error: function (error) {
            console.log(error);
            alert("Si è verificato un errore!");
        }
    });

    $("#btn_upload").click(function (event) {
        event.preventDefault();
        $.ajax({
            url: base_url + "data/upload_image.php",
            type: "POST",
            data: new FormData($("#form_upload_image")[0]),
            enctype: 'multipart/form-data',
            processData: false,  // Important!
            contentType: false,
            cache: false,
            success: function (result) {
                console.log(result);
                if (result){
                    alert("Salvato.");
                    $('#modal_upload_image').modal("hide");
                    $('#photo').val($("#form_upload_image #image_path").val());
                    $('#selected-photo').css("background-image", "url(" + path + ")");
                    loadPhotos();
                } else {
                    alert("Si è verificato un errore!");
                }
            },
            error: function (error) {
                console.log(error);
                alert("Si è verificato un errore!");
            }
        });
    });

    $("#btn_desc_it").click(function () {
        $('.row.description').show();
        $('.row.description_en').hide();
    });

    $("#btn_desc_en").click(function () {
        $('.row.description').hide();
        $('.row.description_en').show();
    });

    function normalizeTimestamp(timestamp) {
        const time = new Date(timestamp);
        const now = new Date();
        let result = "";
        if (time.getMonth() === now.getMonth() && time.getFullYear() === now.getFullYear()){
            if (time.getDate() === now.getDate()){
                result = "Oggi, "
            } else if (time.getDate() === now.getDate() - 1){
                result = "Ieri, "
            } else {
                result = addZero(time.getDate()) + "/" + addZero(eval(time.getMonth() + 1)) + ", ";
            }
        } else {
            result = addZero(eval(time.getMonth() + 1)) + "/" + time.getFullYear() + ", ";
        }
        result += addZero(time.getHours()) + ":" + addZero(time.getMinutes());
        return result;
    }

    function addZero(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }

});