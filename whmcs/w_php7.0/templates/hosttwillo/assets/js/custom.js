"use strict";
// Start [ Preloader ]
$(window).on('load', function() {
    $('.loader-bg').fadeOut('slow', function() {
        $(this).remove();
    });
});
// End [ Preloader ]

// Start [ owl-carousel ]
$('.wesite-slide').owlCarousel({
    loop: true,
    margin: 0,
    autoplay: true,
    autoplaySpeed: 1000,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 2,
            nav: false
        },
    }
});
$('.offer-slide').owlCarousel({
    loop: true,
    margin: 0,
    autoplay: true,
    autoplaySpeed: 1000,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 1,
            nav: false
        },
        1000: {
            items: 1,
            nav: true,
            loop: true
        }
    }
});
$('.domain-slide').owlCarousel({
    loop: true,
    margin: 20,
    autoplay: true,
    autoplaySpeed: 1000,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 2,
            nav: false
        },
        900: {
            items: 3,
            nav: false
        },
        1200: {
            items: 4,
            nav: false,
            loop: true
        },
        1250: {
            items: 4,
            nav: true,
            loop: true
        }
    }
});
$('.client-slide').owlCarousel({
    loop: true,
    margin: 0,
    autoplay: true,
    autoplaySpeed: 1000,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 3,
            nav: false
        },
        1000: {
            items: 5,
            nav: false,
            loop: true
        }
    }
});
$('.faq-slide').owlCarousel({
    loop: true,
    margin: 0,
    autoplay: true,
    autoplaySpeed: 1000,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: false
        },
        600: {
            items: 1,
            nav: false
        },
        1000: {
            items: 1,
            nav: true,
            loop: true
        }
    }
});
$(".owl-prev").html('<i class="fa fa-chevron-left"></i>');
$(".owl-next").html('<i class="fa fa-chevron-right"></i>');
// end [ owl-carousel ]

// Start [ Counter ]
$(document).ready(function() {
    $('.counter').counterUp({
        delay: 30, // the delay time in ms
        time: 1000 // the speed time in ms
    });
});
// End [ Counter ]

// Start [ particals ]
var particlesSettings = {
    particles: {
        number: {
            value: 80,
            density: {
                enable: true,
                value_area: 1000
            }
        },
        color: {
            value: "#FFF"
        },
        shape: {
            type: "circle",
            stroke: {
                width: 0,
                color: "#F0F0F0"
            },
            polygon: {
                nb_sides: 5
            },
            image: {
                src: "img/github.svg",
                width: 100,
                height: 100
            }
        },
        opacity: {
            value: 0.1,
            random: false,
            anim: {
                enable: false,
                speed: 2,
                opacity_min: 0.1,
                sync: false
            }
        },
        size: {
            value: 3,
            random: true,
            anim: {
                enable: false,
                speed: 10,
                size_min: 0.1,
                sync: false
            }
        },
        line_linked: {
            enable: true,
            distance: 150,
            color: "#FFF",
            opacity: 0.4,
            width: 1
        },
        move: {
            enable: true,
            speed: 1,
            direction: "none",
            random: false,
            straight: false,
            out_mode: "out",
            bounce: false,
            attract: {
                enable: false,
                rotateX: 600,
                rotateY: 1200
            }
        }
    },
    interactivity: {
        detect_on: "canvas",
        events: {
            onhover: {
                enable: true,
                mode: "grab"
            },
            onclick: {
                enable: true,
                mode: "push"
            },
            resize: true
        },
        modes: {
            grab: {
                distance: 140,
                line_linked: {
                    opacity: 1
                }
            },
            bubble: {
                distance: 400,
                size: 5,
                duration: 2,
                opacity: 8,
                speed: 1.5
            },
            repulse: {
                distance: 200,
                duration: 0.4
            },
            push: {
                particles_nb: 4
            },
            remove: {
                particles_nb: 2
            }
        }
    },
    retina_detect: true
};
if ($('#particles').length !== 0) {
    particlesJS('particles', particlesSettings);
}

// End [ particals ]

// Start [ Magnific ]
$('.video-play').magnificPopup({
    type: 'iframe',
    closeOnBgClick: false,
    iframe: {
        markup: '<div class="mfp-iframe-scaler">' +
            '<div class="mfp-close"></div>' +
            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
            '<div class="mfp-title">Some caption</div>' +
            '</div>'
    },
    callbacks: {
        markupParse: function(template, values, item) {
            values.title = item.el.attr('title');
        }
    },
    removalDelay: 300,
    mainClass: 'mfp-fade'
});
// End [ Magnific ]

// Start [ Menu ]
$(document).ready(function() {
    $(".dropdown-toggle").hover(function() {
        var dropdownMenu = $(this).children(".dropdown-menu");
        if (dropdownMenu.is(":visible")) {
            dropdownMenu.parent().toggleClass("open");
        }
    });
});
// End [ Menu ]

// Start [ Particals ]
var particlesSettings = {
    particles: {
        number: {
            value: 300,
            density: {
                enable: true,
                value_area: 800
            }
        },
        color: {
            value: "#FFF"
        },
        shape: {
            type: "circle",
            stroke: {
                width: 0,
                color: "#F0F0F0"
            },
            polygon: {
                nb_sides: 5
            },
            image: {
                src: "img/github.svg",
                width: 100,
                height: 100
            }
        },
        opacity: {
            value: 1,
            random: false,
            anim: {
                enable: false,
                speed: 0.5,
                opacity_min: 0.1,
                sync: false
            }
        },
        size: {
            value: 3,
            random: true,
            anim: {
                enable: false,
                speed: 10,
                size_min: 0.1,
                sync: false
            }
        },
        line_linked: {
            enable: false,
            distance: 150,
            color: "#FFF",
            opacity: 0.4,
            width: 1
        },
        move: {
            enable: true,
            speed: 3,
            direction: "none",
            random: false,
            straight: false,
            out_mode: "out",
            bounce: false,
            attract: {
                enable: false,
                rotateX: 600,
                rotateY: 1200
            }
        }
    },
    interactivity: {
        detect_on: "canvas",
        events: {
            onhover: {
                enable: false,
                mode: "grab"
            },
            onclick: {
                enable: false,
                mode: "push"
            },
            resize: true
        },
        modes: {
            grab: {
                distance: 140,
                line_linked: {
                    opacity: 1
                }
            },
            bubble: {
                distance: 400,
                size: 5,
                duration: 2,
                opacity: 8,
                speed: 1.5
            },
            repulse: {
                distance: 200,
                duration: 0.4
            },
            push: {
                particles_nb: 4
            },
            remove: {
                particles_nb: 2
            }
        }
    },
    retina_detect: true
};
if ($('.video-bg').length !== 0) {
    particlesJS('video', particlesSettings);
}
// End [ Particals ]
