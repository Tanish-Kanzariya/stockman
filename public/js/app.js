     window.addEventListener('load', function () {

        const preloader = document.getElementById('stockman-preloader');

        if (preloader) {
            preloader.classList.add('hide');

            setTimeout(function () {
                preloader.remove();
            }, 600);
        }

    });
    
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    const navGroupToggle = document.querySelectorAll('.nav-group-toggle');


    const desktopSidebarToggle = document.getElementById('desktopSidebarToggle')

    sidebarToggle.addEventListener('click',()=>{
        sidebar.classList.add('active');
        sidebarOverlay.classList.add('active');
    });

    sidebarClose.addEventListener('click', ()=>{
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });

    sidebarOverlay.addEventListener('click', ()=>{
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });

    desktopSidebarToggle.addEventListener('click', ()=>{
        sidebar.classList.toggle('collapsed');
    });

    navGroupToggle.forEach(toggle => {
        toggle.addEventListener('click', ()=>{
            const currentGroup = toggle.closest('.nav-group');

            document.querySelectorAll('.nav-group').forEach(group=>{
                if(group !== currentGroup){
                    group.classList.remove('open');
                }
            })

            currentGroup.classList.toggle('open');
        });
    });
