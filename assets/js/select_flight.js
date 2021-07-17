// Sélection des vols pour la recherche

let list_departure = document.querySelectorAll('.departure_select');
let list_return = document.querySelectorAll('.return_select');

if (list_departure.length > 0) {
    let input_departure = document.querySelector('#input_departure');
    let selected_departure = checked(list_departure[0], list_departure[0], input_departure)
    
    list_departure.forEach(elem => {
        elem.addEventListener('click', e => {
            selected_departure = checked(elem, selected_departure, input_departure)
       });
    });
}

if (list_return.length > 0) {
    let input_return = document.querySelector('#input_return');
    let selected_return = checked(list_return[0], list_return[0], input_return)
    
    list_return.forEach(elem => {
        elem.addEventListener('click', e => {
            selected_return = checked(elem, selected_return, input_return)
       });
    });
}

function checked(elem, selected, input)
{
    if (selected.classList.contains('selected_row')) {
        selected.classList.remove('selected_row');
    }
    selected = elem;
    selected.classList.add('selected_row');

    input.value = selected.id;
    input.setAttribute('checked', 'checked');
    
    return selected;
}