const Sidebar = {

    init() {
        this.toggle();
    },

    toggle() {
        $('#sidebarToggle').on('click', function (e) {
            e.stopPropagation();
            if (window.innerWidth < 992) {
                $('body').toggleClass('sidebar-open');
            } else {
                $('body').toggleClass('sidebar-collapse');
            }
        });

        $('#sidebarBackdrop').on('click', function () {
            $('body').removeClass('sidebar-open');
        });

        $(window).on('resize', function () {
            if (window.innerWidth >= 992) {
                $('body').removeClass('sidebar-open');
            }
        });

        $('.sidebar-menu a:not([data-bs-toggle="collapse"])').on('click', function () {
            if (window.innerWidth < 992) {
                $('body').removeClass('sidebar-open');
            }
        });
    }

};

$(function () {
    Sidebar.init();
});