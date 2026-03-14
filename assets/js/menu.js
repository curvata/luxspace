// Menu burger

export function menuStart()
{
    let btn_burger = document.querySelector('.topbar_btn_menu_mobile');
    let menu = document.querySelector('.topbar_menu').cloneNode(true);
    let body = document.querySelector('body');
    let modal = document.createElement('div');

    function closeMenu() {
        if (!modal.classList.contains('menu_full_window')) return;
        document.documentElement.style.overflow = 'visible';
        btn_burger.classList.remove('topbar_open');
        modal.classList.add('menu_closing');
        modal.addEventListener('animationend', () => {
            modal.classList.remove('menu_full_window');
            modal.classList.remove('menu_closing');
            modal.remove();
            menu = document.querySelector('.topbar_menu').cloneNode(true);
        }, { once: true });
    }

    btn_burger.addEventListener('click', e => {
        e.stopPropagation();

        if (modal.classList.contains('menu_full_window')) {
            closeMenu();
        } else {
            document.documentElement.style.overflow = 'hidden';
            modal.classList.add('menu_full_window');
            menu.classList.add('mobile');
            btn_burger.classList.add('topbar_open');
            modal.appendChild(menu);
            body.append(modal);
        }
    });

    body.addEventListener('click', e => {
        closeMenu();
    });

    window.addEventListener('resize', e => {
        closeMenu();
    });
}