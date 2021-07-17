import Splide from '@splidejs/splide';

// Galerie d'images page destination

new Splide( '.splide', {
    heightRatio: 1,
    autoplay: true,
    height: '100%',
    width: '100%',
    cover: true,
	type   : 'loop',
    lazyLoad   : 'sequential',
} ).mount();