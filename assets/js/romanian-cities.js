jQuery(document).ready(function($) {
    var citiesData = null;
    
    // Funcție pentru a încărca JSON-ul cu localități
    function loadCitiesData(callback) {
        if (citiesData !== null) {
            callback(citiesData);
            return;
        }
        
        // URL-ul catre fisierul json, asumat ca fiind in tema (injectat prin wp_localize_script)
        if (typeof romanianCitiesObj === 'undefined' || !romanianCitiesObj.jsonUrl) {
            console.error("Romanian cities JSON URL not defined.");
            return;
        }

        $.getJSON(romanianCitiesObj.jsonUrl, function(data) {
            citiesData = data;
            callback(citiesData);
        }).fail(function() {
            console.error("Eroare la incarcarea fisierului cu localitati.");
        });
    }

    // Funcție pentru a actualiza dropdown-ul de orașe
    function updateCityDropdown(type) {
        var countryField = $('#' + type + '_country');
        var stateField = $('#' + type + '_state');
        var cityField = $('#' + type + '_city');
        
        if (stateField.length === 0 || cityField.length === 0) return;

        var selectedCountry = countryField.length ? countryField.val() : 'RO'; // Default to RO if not found (e.g. some forms might omit it)
        var selectedStateCode = stateField.val();
        var selectedStateName = stateField.find('option:selected').text();
        var currentCity = cityField.val();

        // Dacă țara NU este România, transformăm localitatea într-un câmp text normal (pentru a scrie manual)
        if (selectedCountry !== 'RO') {
            if (cityField.is('select')) {
                var inputHTML = '<input type="text" class="input-text" name="' + type + '_city" id="' + type + '_city" value="' + currentCity + '">';
                cityField.replaceWith(inputHTML);
            }
            return;
        }

        if (!selectedStateCode || selectedStateCode === '') {
            if (cityField.is('select')) {
                cityField.empty().append('<option value="">Selectează un județ mai întâi...</option>');
            }
            return;
        }

        loadCitiesData(function(data) {
            var cities = [];
            
            // Verificam in functie de formatul JSON-ului.
            // Daca e un Array, cum ar fi cel descarcat recent.
            if (Array.isArray(data)) {
                $.each(data, function(index, item) {
                    var county = item.county || item.judet;
                    var city = item.city || item.nume || item.localitate;
                    
                    // Bucuresti e un caz special (deseori county e gol)
                    if (selectedStateCode === 'B' && (city === 'Bucuresti' || county === 'Bucuresti' || county === 'B')) {
                        cities.push(city);
                    } else if (county && (county === selectedStateName || selectedStateName.indexOf(county) !== -1 || county === selectedStateCode)) {
                        cities.push(city);
                    }
                });
            } else if (data[selectedStateCode]) {
                cities = data[selectedStateCode];
            } else if (data[selectedStateName]) {
                cities = data[selectedStateName];
            }

            if (cities.length === 0) {
                if (cityField.is('select')) {
                    var inputHTML = '<input type="text" class="input-text" name="' + type + '_city" id="' + type + '_city" value="' + currentCity + '">';
                    cityField.replaceWith(inputHTML);
                }
                return;
            }

            // Remove duplicates and sort
            cities = cities.filter(function(item, pos) {
                return cities.indexOf(item) == pos;
            });
            cities.sort();

            var optionsHTML = '<option value="">Selectează localitatea...</option>';
            var foundCurrent = false;
            
            $.each(cities, function(index, cityName) {
                var selected = (cityName === currentCity) ? ' selected="selected"' : '';
                if (cityName === currentCity) foundCurrent = true;
                optionsHTML += '<option value="' + cityName + '"' + selected + '>' + cityName + '</option>';
            });

            if (cityField.is('select')) {
                cityField.html(optionsHTML);
                // Selectul e de tip dropdown acum, il forțăm să selecteze valoarea curentă dacă există
                if (foundCurrent) {
                    cityField.val(currentCity);
                }
            } else {
                var selectHTML = '<select name="' + type + '_city" id="' + type + '_city" class="state_select select2-hidden-accessible" data-placeholder="Selectează localitatea">' + optionsHTML + '</select>';
                cityField.replaceWith(selectHTML);
                
                // WooCommerce re-init for select2
                if ($.fn.select2) {
                    $('#' + type + '_city').select2();
                }
            }
        });
    }

    // Ascultam evenimentele pe dropdown-urile de tara si judet
    $('form.checkout, form.edit-address').on('change', '#billing_state, #billing_country', function() {
        updateCityDropdown('billing');
    });

    $('form.checkout, form.edit-address').on('change', '#shipping_state, #shipping_country', function() {
        updateCityDropdown('shipping');
    });

    // Initializam la incarcare
    setTimeout(function() {
        if ($('#billing_state').length || $('#billing_country').length) updateCityDropdown('billing');
        if ($('#shipping_state').length || $('#shipping_country').length) updateCityDropdown('shipping');
    }, 500);
});
