jQuery(document).ready(async function($) {
    const useMapForAddress = phpVars?.useMapForAddress === "yes";
    const useAreaDropdown = phpVars?.useAreaDropdown === "yes";

    const groups = ["billing", "shipping"];
    /**
     * Used for filtering postcodes by district
     */
    let biteshipAreas = new Map(groups.map(group => [group, []]));

    function fetchCities(wooStateId, group) {
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'get_adm_lvl_2',
                woo_state_id: wooStateId, 
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    populateCitiesDropdown(data.data.areas, group);
                } else {
                    console.error('Failed to fetch cities:', data.data.message);
                }
            })
            .catch((error) => console.error('Error:', error));
    }

    function populateCitiesDropdown(areas, group) {
        const dropdown = document.querySelector(`#${group}_city`);
        dropdown.innerHTML = '';
        dropdown.add(new Option('Select a city', ''));
        areas.forEach((area) => {
            dropdown.add(new Option(area.administrative_division_level_2_name, area.administrative_division_level_2_id));
        });
    }

    function fetchDistricts(admlLvl2Id, group) {
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'get_adm_lvl_3',
                administrative_division_level_2_id: admlLvl2Id,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // the provided areas already include the postcodes for each district
                    const areas = data.data.areas;
                    biteshipAreas.set(group, areas);
                    populateDistrictsDropdown(data.data.areas, group);
                } else {
                    console.error('Failed to fetch districts:', data.data.message);
                }
            })
            .catch((error) => console.error('Error:', error));
    }

    function populateDistrictsDropdown(areas, group) {
        const dropdown = document.querySelector(`#${group}_district`);
        dropdown.innerHTML = '';

        const districtMap = areas.reduce((acc, area) => {
            acc[area.administrative_division_level_3_id] = area.administrative_division_level_3_name;
            return acc;
        }, {});
        const districts = Object.entries(districtMap).map(([id, name]) => ({ id, name }));

        dropdown.add(new Option('Select a district', ''));
        districts.forEach((district) => {
            dropdown.add(new Option(district.name, district.id));
        });

        populatePostcodeDropdown(areas, group);
    }

    function populatePostcodeDropdown(areas, group) {
        const dropdown = document.querySelector(`#${group}_postcode`);
        dropdown.innerHTML = '';

        // create unique postcodes
        const postcodeMap = areas.reduce((acc, area) => {
            acc[area.postal_code] = area.postal_code;
            return acc;
        }, {});
        const postcodes = Object.entries(postcodeMap).map(([id, name]) => ({ id, name }));

        dropdown.add(new Option('Select a postcode', ''));
        postcodes.forEach((postcode) => {
            dropdown.add(new Option(postcode.name, postcode.id));
        });
    }

    function filterPostcodeDropdown(admlLvl3Id, group) {
        const dropdown = document.querySelector(`#${group}_postcode`);
        dropdown.innerHTML = '';

        const areas = biteshipAreas.get(group).filter((area) => area.administrative_division_level_3_id === admlLvl3Id);

        // create unique postcodes
        const postcodeMap = areas.reduce((acc, area) => {
            acc[area.postal_code] = area.postal_code;
            return acc;
        }, {});
        const postcodes = Object.entries(postcodeMap).map(([id, name]) => ({ id, name }));

        dropdown.add(new Option('Select a postcode', ''));
        postcodes.forEach((postcode) => {
            dropdown.add(new Option(postcode.name, postcode.id));
        });
    }

    function coordinateString(location) {
        return `${location.lat()},${location.lng()}`;
    }

    function getAddressFromPlace(place) {
        const address1Object = place.address_components.reduce(
            (address, component) => {
                if (component.types.includes("route")) {
                    address.route = component.short_name;
                }

                if (component.types.includes("street_number")) {
                    address.streetNumber = component.short_name;
                }

                return address;
            },
            { route: "", streetNumber: "" },
        );

        const address2Object = place.address_components.reduce(
            (address, component) => {
                if (component.types.includes("administrative_area_level_3")) {
                    address.district = component.short_name;
                }

                if (component.types.includes("administrative_area_level_4")) {
                    address.subdistrict = component.short_name;
                }

                return address;
            },
            { district: "", subdistrict: "" },
        );

        return {
            address1: `${address1Object.route} ${address1Object.streetNumber}`.trim(),
            address2: `${address2Object.subdistrict}, ${address2Object.district}`.trim(),
            postalCode: place.address_components.find((component) => component.types.includes("postal_code"))?.short_name,
            city: place.address_components.find((component) => component.types.includes("administrative_area_level_2"))?.short_name,
            province: place.address_components.find((component) => component.types.includes("administrative_area_level_1"))?.short_name,
        };
    }

    function updateOtherCoordinate(group, coordinateValue) {
        const otherGroup = groups.find((i) => i !== group);
        if (!otherGroup) return;

        const otherCoordinateInput = $(`#${otherGroup}_coordinate`);
        if (!otherCoordinateInput.length) return;

        otherCoordinateInput.val(coordinateValue).trigger("update_checkout", {
            update_shipping_method: true,
        });
    }

    function updateCoordinate(group, coordinateInput, location) {
        const coordinateValue = coordinateString(location);
        coordinateInput.val(coordinateValue);

        if (!isBillingOnly() && !isShipToDifferentAddress()) {
            return updateOtherCoordinate(group, coordinateValue);
        }

        coordinateInput.trigger("update_checkout", { update_shipping_method: true });
    }

    function updateOtherAddress(group, place) {
        const otherGroup = groups.find((i) => i !== group);
        if (!otherGroup) return;

        const address1Input = $(`#${otherGroup}_address_1`);
        const address2Input = $(`#${otherGroup}_address_2`);
        const postalCodeInput = $(`#${otherGroup}_postcode`);
        const cityInput = $(`#${otherGroup}_city`);
        // const provinceInput = $(`#${otherGroup}_state`);

        const { address1, address2, postalCode, city } = getAddressFromPlace(place);

        if (address1Input.length && address1) {
            address1Input.val(address1);
        }

        if (address2Input.length && address2) {
            address2Input.val(address2);
        }

        // Update only if input is not dropdown
        // NOTE: useAreaDropdown validation can be removed if we found the solution
        if (postalCodeInput.length && postalCode && !useAreaDropdown) {
            postalCodeInput.val(postalCode);
        }

        if (cityInput.length && city && !useAreaDropdown) {
            cityInput.val(city);
        }

        // Commented so it doesn't auto update a dropdown, because if it doesn't match, it can return empty string in billing information
        // if (provinceInput.length && province) {
        //     provinceInput.val(province);
        // }
    }

    function updateAddress(group, place) {
        const address1Input = $(`#${group}_address_1`);
        const address2Input = $(`#${group}_address_2`);
        const postalCodeInput = $(`#${group}_postcode`);
        const cityInput = $(`#${group}_city`);
        // const provinceInput = $(`#${group}_state`);

        const { address1, address2, postalCode, city } = getAddressFromPlace(place);

        if (address1Input.length && address1) {
            address1Input.val(address1);
        }

        if (address2Input.length && address2) {
            address2Input.val(address2);
        }

        // Update only if input is not dropdown
        // NOTE: useAreaDropdown can be removed if we found the solution
        if (postalCodeInput.length && postalCode && !useAreaDropdown) {
            postalCodeInput.val(postalCode);
        }

        if (cityInput.length && city && !useAreaDropdown) {
            cityInput.val(city);
        }

        // Commented so it doesn't auto update a dropdown, because if it doesn't match, it can return empty string in billing information
        // if (provinceInput.length && province) {
        //     provinceInput.val(province);
        // }

        if (!isBillingOnly() && !isShipToDifferentAddress()) {
            return updateOtherAddress(group, place);
        }
    }

    function isBillingOnly() {
        return !document.getElementById("ship-to-different-address-checkbox");
    }

    function isShipToDifferentAddress() {
        const shipToDifferentAddressSelector = "#ship-to-different-address-checkbox";
        return $(shipToDifferentAddressSelector).is(":checked");
    }

    $("form.woocommerce-checkout").on("change", 'input[name="payment_method"]', function() {
        $(document.body).trigger("update_checkout", {
            update_shipping_method: true,
        });
    });

    async function addCoordinateListener(group) {
        const mapId = `${group}_map`;
        const searchBoxId = `${group}_map_searchbox`;
        const groupSelector = `.woocommerce-${group}-fields__field-wrapper`;
        const coordinateFieldSelector = `#${group}_coordinate_field`;
        const coordinateInputSelector = `#${group}_coordinate`;
        const coordinateField = $(coordinateFieldSelector);
        const coordinateInput = $(coordinateInputSelector);

        if (!coordinateField.length || !coordinateInput.length) return;

        coordinateInput.on("change", function() {
            $("body").trigger("update_checkout");
        });
        coordinateField.hide();

        const mapWrapper = $("<p>", { class: "woocommerce-map-wrapper form-row form-row-wide" });
        const mapLabel = $("<label>", { text: checkoutI18n.LOCATION_LABEL });
        const mapContainer = $("<div>", { class: "woocommerce-map-container", id: mapId });
        const [lat, lng] = coordinateInput.val()?.split(",") || [];
        const defaultCoordinate = { lat: parseFloat(lat) || -6.175392, lng: parseFloat(lng) || 106.8271528 };

        mapWrapper.append(mapLabel);
        mapWrapper.append(mapContainer);

        if (!$(groupSelector).length) {
            return;
        }

        $(groupSelector).after(mapWrapper);

        const searchBoxWrapper = $("<span>", { class: "woocommerce-searchbox-wrapper gm-control-active gm-fullscreen-control" });
        const searchBoxInput = $("<input>", { id: searchBoxId, placeholder: checkoutI18n.SEARCHBOX_PLACEHOLDER });

        searchBoxInput.on("keydown", function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });

        searchBoxWrapper.append(searchBoxInput);

        const map = new google.maps.Map(document.getElementById(mapId), {
            center: defaultCoordinate,
            zoom: 13,
            streetViewControl: false,
            mapTypeControl: false,
            fullscreenControl: true,
            fullscreenControlOptions: {
                position: google.maps.ControlPosition.BOTTOM_LEFT,
            },
        });

        const marker = new google.maps.Marker({
            position: defaultCoordinate,
            map: map,
        });

        const searchBox = new google.maps.places.SearchBox(searchBoxInput.get(0));

        map.controls[google.maps.ControlPosition.TOP_LEFT].push(searchBoxWrapper.get(0));

        map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });

        map.addListener("click", (event) => {
            marker.setPosition(event.latLng);
            updateCoordinate(group, coordinateInput, event.latLng);
        });

        map.addListener("click", (event) => {
            event.stop();
        });

        searchBox.addListener("places_changed", function() {
            const places = searchBox.getPlaces();
            if (places.length === 0) return;

            const bounds = new google.maps.LatLngBounds();
            places.forEach((place) => {
                if (!place.geometry) return;

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }

                marker.setPosition(place.geometry.location);
                if (useMapForAddress) {
                    updateAddress(group, place);
                }

                updateCoordinate(group, coordinateInput, place.geometry.location);
            });

            map.fitBounds(bounds);
        });
    }

    function addLocationDropdownListeners(group) {
        const stateDropdownSelector = `#${group}_state`;
        const cityDropdownSelector = `#${group}_city`;
        const districtDropdownSelector = `#${group}_district`;
        const postcodeDropdownSelector = `#${group}_postcode`;
        const stateDropdown = $(stateDropdownSelector);
        const cityDropdown = $(cityDropdownSelector);
        const districtDropdown = $(districtDropdownSelector);
        const postcodeDropdown = $(postcodeDropdownSelector);

        populateCitiesDropdown([], group); 
        populateDistrictsDropdown([], group); 
        populatePostcodeDropdown([], group);
        
        stateDropdown.on("change", function() {
            biteshipAreas.set(group, []);
            const wooStateId = stateDropdown.val();
            if (!wooStateId) { // If state is not selected
                cityDropdown.val('').trigger('change');
                populateCitiesDropdown([], group); 
            } else {
                fetchCities(wooStateId, group);
                populateDistrictsDropdown([], group);
            }
        });

        cityDropdown.on("change", function() {
            biteshipAreas.set(group, []);
            const cityId = cityDropdown.val();
            if (!cityId) {
                districtDropdown.val('').trigger('change');
                populateDistrictsDropdown([], group); 
            } else {
                const cityName = cityDropdown.find('option:selected').text();
                $(`#${group}_city_name`).val(cityName);
                fetchDistricts(cityId, group);
            }
        });

        districtDropdown.on("change", function() {
            const districtId = districtDropdown.val();
            if (districtId) {
                postcodeDropdown.val('').trigger('change');
                filterPostcodeDropdown(districtId, group);
            }
        });

        postcodeDropdown.on("change", function() {
            const postcode = postcodeDropdown.val();
            if (postcode) {
                $("body").trigger("update_checkout"); // refresh checkout details to refetch shipping carriers
            }
        });
    }

    groups.forEach((group) => {
        const coordinateFieldSelector = `#${group}_coordinate_field`;
        const coordinateInputSelector = `#${group}_coordinate`;
        const coordinateField = $(coordinateFieldSelector);
        const coordinateInput = $(coordinateInputSelector);
        
        if (coordinateField.length && coordinateInput.length) addCoordinateListener(group);
        if (useAreaDropdown) addLocationDropdownListeners(group);
    });
});
