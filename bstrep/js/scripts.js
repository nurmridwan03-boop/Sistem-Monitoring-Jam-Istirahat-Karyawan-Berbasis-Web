/*!
* Start Bootstrap - Simple Sidebar v6.0.6 (https://startbootstrap.com/template/simple-sidebar)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-simple-sidebar/blob/master/LICENSE)
*/
// 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
    
    // Close sidebar when clicking on overlay (mobile)
    document.body.addEventListener('click', function(e) {
        if (window.innerWidth <= 767.98) {
            const sidebar = document.querySelector('#sidebar-wrapper');
            const sidebarToggle = document.querySelector('#sidebarToggle');
            if (sidebar && document.body.classList.contains('sb-sidenav-toggled')) {
                // Jika klik di luar sidebar dan bukan pada toggle button
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    document.body.classList.remove('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', 'false');
                }
            }
        }
    });
    
    // Prevent sidebar click from closing sidebar
    const sidebar = document.querySelector('#sidebar-wrapper');
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

});
