const BASE_URL = "https://www.niceplaces.it/";
//const BASE_URL = "http://localhost/niceplaces/";

function get_lang(){
    if (window.location.pathname.includes("/en/")){
        return "en";
    } else {
        return "it";
    }
}

function formatDistance(n) {
    if (n < 1){
        n = n*1000;
        return (n + " m").replace(".", ",");
    } else if (n < 100){
        return (n.toFixed(1) + " km").replace(".", ",");
    } else {
        return (n.toFixed(0) + " km").replace(".", ",");
    }
}