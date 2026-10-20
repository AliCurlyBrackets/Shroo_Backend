<?php
//******************** change admin panel theme style ********************//
function adminStyle() {
	?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Changa:wght@500&display=swap');
        :root{
            --bpa-pt-main-green : #b02a4c !important;
        }
        

        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'Changa', sans-serif !important;
        }

        body::-webkit-scrollbar-track {
            background: #fff;
        }

        body::-webkit-scrollbar-thumb {
            background: linear-gradient(80deg, #b02a4c 20%, #b02a4c 100%);
            border-radius: 50px;
        }

        body::-webkit-scrollbar {
            width: .7em;
        }

        .wp-core-ui .button-primary, #wpadminbar, #adminmenu, #adminmenuback, #adminmenuwrap, #adminmenu .wp-has-current-submenu .wp-submenu, #adminmenu .wp-has-current-submenu.opensub .wp-submenu, #adminmenu .wp-submenu, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu {
            background: linear-gradient(80deg, #b02a4c 20%, #b02a4c 100%);
        }

        #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, .folded #adminmenu li.current.menu-top, #adminmenu a:hover, #adminmenu li.menu-top:hover, #adminmenu li.opensub > a.menu-top, #adminmenu li > a.menu-top:focus, #adminmenu .awaiting-mod, #adminmenu .update-plugins, #wpadminbar .menupop .ab-sub-wrapper, #wpadminbar .shortlink-input {
            background: #040B36 !important;
        }

        #adminmenu .wp-submenu .wp-submenu-head, #adminmenu .wp-has-current-submenu .wp-submenu a, #adminmenu .wp-has-current-submenu.opensub .wp-submenu a, #adminmenu .wp-submenu a, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu a, .folded #adminmenu .wp-has-current-submenu .wp-submenu a {
            color: #e6e7e6 !important;
        }

        body {
            background: #FFF !important;
        }

        #wpadminbar #wp-admin-bar-language > div.ab-item, #wpadminbar #wp-admin-bar-language ul li {
            background-color: transparent;
        }

        #adminmenu .awaiting-mod, #adminmenu .update-plugins, #wpadminbar .menupop .ab-sub-wrapper, #wpadminbar .shortlink-input {
            background: #243935;
        }

        #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, .folded #adminmenu li.current.menu-top, #adminmenu a:hover, #adminmenu li.menu-top:hover, #adminmenu li.opensub > a.menu-top, #adminmenu li > a.menu-top:focus {
            background-color: #6b702e;
        }

        #adminmenu div.wp-menu-image:before, #adminmenu a, .wp-core-ui .button-primary.focus, .wp-core-ui .button-primary.hover, .wp-core-ui .button-primary:focus, .wp-core-ui .button-primary:hover, .wp-core-ui .button-primary, #collapse-button, #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, #adminmenu .wp-menu-arrow, #adminmenu .wp-menu-arrow div, #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, .folded #adminmenu li.current.menu-top, .folded #adminmenu li.wp-has-current-submenu, #wpadminbar .ab-empty-item, #wpadminbar a.ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon, #wpadminbar #adminbarsearch:before, #wpadminbar .ab-icon:before, #wpadminbar .ab-item:before {
            color: #fff !important;
        }

        #adminmenu .wp-submenu a:focus, #adminmenu .wp-submenu a:hover, #adminmenu a:hover, #adminmenu li.menu-top>a:focus {
            color: #FFF !important;
        }

        #adminmenu .opensub .wp-submenu li.current a, #adminmenu .wp-submenu li.current, #adminmenu .wp-submenu li.current a, #adminmenu .wp-submenu li.current a:focus, #adminmenu .wp-submenu li.current a:hover, #adminmenu a.wp-has-current-submenu:focus + .wp-submenu li.current a {
            color: #fff !important;
        }
    span.bookingpress_logo_btn {
        display: none;
    }

    </style>
	<?php
}

add_action( 'admin_notices', 'adminStyle' );