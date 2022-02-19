$.fn.isInViewport = function() {
    let elementTop = $(this).offset().top;
    let elementBottom = elementTop + $(this).outerHeight();
    let viewportTop = $(window).scrollTop();
    let viewportBottom = viewportTop + $(window).height();
    return elementBottom > viewportTop && elementTop < viewportBottom;
};

$(document).ready(function () {

    let userLang = navigator.language || navigator.userLanguage;
    let targetURL = "http://www.niceplaces.it/en/";
    if (userLang.localeCompare("it-IT") === 0){
        targetURL = "http://www.niceplaces.it/";
    }

    $('.nav-link').click(function () {
        let id = $(this).attr('href').substr(1);
        $('html,body').animate({
            scrollTop: $('a[name=' + id + ']').offset().top
        }, 'slow');
    });

    if (window.location.search.includes('lang=en')){
        $('.dropdown.lang .dropdown-item.flag-en').trigger('click');
    }

    $(function(){
        let navMain = $(".navbar-collapse"); // avoid dependency on #id
        // "a:not([data-toggle])" - to avoid issues caused
        // when you have dropdown inside navbar
        navMain.on("click", "a:not([data-toggle])", null, function () {
            navMain.collapse('hide');
        });
    });

    $('.container').fitVids();
});

let counters_loaded = false;

$(window).on('scroll', function() {
    if (!counters_loaded && $('.row.counters').isInViewport()){
        counters_loaded = true;
        $('.counter').each(function() {
            let $this = $(this);
            let countTo = $this.attr('data-count');
            $({ countNum: $this.text()}).animate(
                {
                    countNum: countTo
                },{
                    duration: 1000,
                    easing: 'linear',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum);
                    }
                });
        });
        $('.counter-plus').each(function() {
            let $this = $(this);
            let countTo = $this.attr('data-count');
            $({ countNum: $this.text()}).animate(
                {
                    countNum: countTo
                },{
                    duration: 1000,
                    easing: 'linear',
                    step: function() {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $this.text(this.countNum.toLocaleString() + "+");
                    }
                });
        });
    }
});
