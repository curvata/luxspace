import '@splidejs/splide/dist/css/splide.min.css';
import Datepicker from 'vanillajs-datepicker/Datepicker';
import fr from 'vanillajs-datepicker/locales/fr';

// Sélecteur de date

Object.assign(Datepicker.locales, fr);

const birthday = document.querySelector('#stringBirthday');
const destination = document.querySelector("#destination");
let id;
let picker1;
let picker2;

if (destination) {
    const departure = document.querySelector('#departure');
    const returned = document.querySelector('#returned');
    let selected = document.querySelector('option[selected]');

    let config = {
        format: 'dd-mm-yyyy',
        minDate: new Date(),
        maxDate: new Date(new Date().getFullYear() +1, new Date().getMonth(), new Date().getDate()),
        clearBtn: true,
        todayHighlight: true,
        language: 'fr'
    }
    picker1 = new Datepicker(departure, config); 
    picker2 = new Datepicker(returned, config); 

    if (selected) {
        id = selected.value;
    } else {
        id = destination.firstChild.value;
    }
    destination.addEventListener('change', e => {
        id = e.target.value;
        getListNoFlights(id);
    });
    getListNoFlights(id);
}

function getListNoFlights(id) {
    fetch('/list-days-no-flights/' + id)
    .then((response) => { return response.json(); })
    .then(response => { 
        picker1.setOptions({datesDisabled: Object.values(response.departures)});
        picker2.setOptions({datesDisabled: Object.values(response.returneds)});
    });
}

if (birthday) {
    const picker = new Datepicker(birthday, {
        format: 'dd-mm-yyyy',
        clearBtn: true,
        todayHighlight: true,
        language: 'fr'
    }); 
}