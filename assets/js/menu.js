// Menu burger

export function menuStart()
{
    let btn_burger = document.querySelector('.topbar_btn_menu_mobile');
    let menu = document.querySelector('.topbar_menu').cloneNode(true);
    let body = document.querySelector('body');
    let modal = document.createElement('div');

    btn_burger.addEventListener('click', e => {
        e.stopPropagation();

        if (modal.classList.contains('menu_full_window')) {
            document.documentElement.style.overflow = 'visible';
            modal.classList.remove('menu_full_window');
            btn_burger.classList.remove('topbar_open');
            modal.remove();
            menu.remove();
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
        document.documentElement.style.overflow = 'visible';
        modal.classList.remove('menu_full_window');
        btn_burger.classList.remove('topbar_open');
        modal.remove();
        menu.remove();
    });

    window.addEventListener('resize', e => {
        btn_burger.classList.remove('topbar_open');
        modal.remove();
        menu.remove();
    });
}