const limit = 100; // API max limit
let userLatitude = null;
let userLongitude = null;
let whereClause = '';

/**
 * Calls the French sports facilities API
 * @param {Object} options - Query options
 * @param {number} options.limit - Maximum number of records to return (default: 100, max: 100)
 * @param {number} options.offset - Number of records to skip (default: 0)
 * @param {string} options.where - Filter query
 * @param {string} options.select - Fields to select
 * @returns {Promise<Object>} API response data
 */
async function getSportsEquipments(options) {
    // Set default options if not provided
    options = options || {};

    const baseUrl = 'https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es/records';

    const params = new URLSearchParams();

    // API has a max limit of 100 per request
    if (options.limit) params.append('limit', Math.min(options.limit, 100));
    if (options.offset) params.append('offset', options.offset);
    if (options.where) params.append('where', options.where);
    if (options.select) params.append('select', options.select);

    const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;

    try {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log(data)
        return data.results;
    } catch (error) {
        console.error('Error fetching sports equipments:', error);
        throw error;
    }
}

/**
 * Fetch multiple pages of results
 * @param {number} totalRecords - Total number of records to fetch
 * @param {Object} options - Query options (where, select)
 * @returns {Promise<Array>} All results
 */
async function getAllSportsEquipments(totalRecords, options = {}) {
    const results = [];
    const totalPages = Math.ceil(totalRecords / limit);
    console.log(`fetching...`);
    for (let i = 0; i < totalPages; i++) {
        const offset = i * limit;
        const pageResults = await getSportsEquipments({
            ...options,
            limit: limit,
            offset: offset
        });

        results.push(...pageResults);
    }
    console.log('fetching complete.');
    return results;
}

/**
 * Create markers and clusters for the map
 * @param {*} data 
 */
function createMarkers(data) {
    if (!data || data.length === 0) {
        console.log('No results found');
        return;
    }

    if(window.markers){
        map.removeLayer(window.markers);
    }
    window.markers = new L.MarkerClusterGroup({
        disableClusteringAtZoom: 15
    });
    

    let noCoordCount = 0;
    for (let facility of data) {
        // Check if coordinates exist - they are in equip_coordonnees.lon and equip_coordonnees.lat
        if (facility.equip_coordonnees && facility.equip_coordonnees.lat && facility.equip_coordonnees.lon) {
            let lat = facility.equip_coordonnees.lat;
            let lon = facility.equip_coordonnees.lon;
            let marker = L.marker([lat, lon]).addTo(markers);

            str = '<b>';
            if(facility.equip_nom){
                str += facility.equip_nom + '</b><br>';
            }
            if(facility.inst_adresse){
                str += facility.inst_adresse + '<br>';
            }
            if(facility.new_name){
                str += facility.new_name;
            }

            if(facility.equip_type_name){
                str += '<br><i>' + facility.equip_type_name + '</i>';
            }

            if(facility.equip_sanit === 'true'){
                str += '<br>• Sanitaires';
            }

            if(facility.equip_douche === 'true'){
                str += '<br>• Douches';
            }

            if(facility.equip_acces_handi_mobilite){
                str += '<br>• Accessible aux personnes en situation de handicap moteur';
            }
            if(facility.equip_acces_handi_sensoriel){
                str += '<br>• Accessible aux personnes en situation de handicap sensoriel';
            }

            btnStyle = 'style="margin-top:5px;padding:5px 10px;background-color:#007bff;color:white;border:none;border-radius:3px;cursor:pointer;"';

            str += '</br><form method="GET" action="../HTML/calendar.php"><button type="submit" name = "infid" value = "'+facility.equip_numero+'" ' + btnStyle + '>Voir les détails</button></form>';

            marker.bindPopup(str);
        } else {
            noCoordCount++;
        }
    }
    markers.addTo(map);
    console.log('Number of facilities without coordinates:', noCoordCount);
}

function createMarkersFromDb(Name, Lat, Lon, Type, Address, infid) {
    // Reuse a single cluster layer so multiple DB markers are kept on the map
    if (!window.markersDb) {
        window.markersDb = new L.MarkerClusterGroup({
            disableClusteringAtZoom: 15
        });
        window.markersDb.addTo(map);
    }

    const marker = L.marker([Lat, Lon]);
    let str = `<b>${Name}</b><br>${Address}<br><i>${Type}</i>`;
    btnStyle = 'style="margin-top:5px;padding:5px 10px;background-color:#007bff;color:white;border:none;border-radius:3px;cursor:pointer;"';
    str += '</br><form method="GET" action="../HTML/calendar.php"><button type="submit" name = "infid" value = "'+infid+'" ' + btnStyle + '>Voir les détails</button></form>';
    marker.bindPopup(str);
    window.markersDb.addLayer(marker);
}

/**
 * Get user's current location
 */
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
}

/**
 * Handle successful geolocation
 * @param {Object} position
 */
function success(position) {
    userLatitude = position.coords.latitude;
    userLongitude = position.coords.longitude;

    console.log(`User location: Lat ${userLatitude}, Lon ${userLongitude}`);
    
    // Create a custom red icon for user location
    const redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Remove existing user marker if present
    if (window.userMarker) {
        map.removeLayer(window.userMarker);
    }

    // Add red user location marker
    window.userMarker = L.marker([userLatitude, userLongitude], { icon: redIcon })
        .addTo(map)
        .bindPopup("<b>Vous êtes ici</b>")
        .openPopup();
    
}

/**
 * Handle geolocation errors
 */
function error() {
    alert("Sorry, no position available.");
}

/**
 * Load map with tile layer and sports facilities markers
 * @param {map} map 
 * @param {string} whereClause - Optional WHERE clause for filtering
 */
function loadmap(map, whereClause = '') {
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution:
            '&copy; <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>'
    }).addTo(map);

    const options = whereClause ? { where: whereClause } : {};
    
    getAllSportsEquipments(limit, options)
        .then(data => {
            console.log('Sports facilities data:', data.length, 'records');
            createMarkers(data);
            const loading = document.getElementById('loading');
            if (loading) loading.classList.add('hidden');
        })
        .catch(error => console.error('Error:', error));
}

/**
 * Fetch equipment types from API
 * @returns {Promise<Array>} Equipment types data
 */
async function getEquipmentTypes() {
    const baseUrl = "https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es-types/records?limit=-1";
    try {
        const response = await fetch(baseUrl);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log('Fetched equipment types successfully');
        return data.results;
    } catch (error) {
        console.error('Error fetching equipment types:', error);
        throw error;
    }
}


// Global state for equipment selection
let allEquipmentTypes = [];
let selectedEquipmentTypes = new Set();
let areaVisible = false;
let amenitiesVisible = false;
let selectedOptions = new Set();

function showEquipmentTypes() {
    const selectionArea = document.getElementById("equipmentSelectionArea");
    const gridContainer = document.getElementById("equipmentGrid");
    const searchInput = document.getElementById("equipmentSearch");
    const rayonInput = document.getElementById("rayon");
    const rayonValue = document.getElementById("rayonValue");
    const toggleBtn = document.getElementById("toggleCheckboxes");
    const amenitiesSection = document.getElementById("amenitiesSelectionArea");
    const toggleAmenitiesBtn = document.getElementById("toggleAmenities");
    
    // Toggle selection area visibility
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            areaVisible = !areaVisible;
            selectionArea.style.display = areaVisible ? 'block' : 'none';
            toggleBtn.classList.toggle('active', areaVisible);
            
            if (areaVisible) {
                toggleBtn.innerHTML = '<span class="checkbox-icon">▲</span> Masquer les types';
            } else {
                toggleBtn.innerHTML = '<span class="checkbox-icon">▼</span> Afficher les types';
            }
        });
    }
    
    // Toggle amenities section visibility
    if (toggleAmenitiesBtn) {
        toggleAmenitiesBtn.addEventListener('click', () => {
            amenitiesVisible = !amenitiesVisible;
            amenitiesSection.style.display = amenitiesVisible ? 'block' : 'none';
            toggleAmenitiesBtn.classList.toggle('active', amenitiesVisible);
            
            if (amenitiesVisible) {
                toggleAmenitiesBtn.innerHTML = '<span class="checkbox-icon">▲</span> Masquer les options';
            } else {
                toggleAmenitiesBtn.innerHTML = '<span class="checkbox-icon">▼</span> Afficher les options';
            }
        });
    }

     // Update range slider display and gradient
    if (rayonInput && rayonValue) {
        const updateSliderGradient = (value) => {
            const percentage = ((value - rayonInput.min) / (rayonInput.max - rayonInput.min)) * 100;
            rayonInput.style.background = `linear-gradient(to right, #add8e6 0%, #add8e6 ${percentage}%, #e0e0e0 ${percentage}%, #e0e0e0 100%)`;
        };
        
        updateSliderGradient(rayonInput.value);
        
        rayonInput.addEventListener('input', (e) => {
            const value = parseInt(e.target.value);
            if (value === 0) {
                rayonValue.textContent = 'Désactivé';
            } else {
                rayonValue.textContent = value + ' km';
            }
            updateSliderGradient(e.target.value);
        });
    }
    
    // Load and display equipment types
    getEquipmentTypes().then(data => {
        allEquipmentTypes = data.sort((a, b) => a.equipement.localeCompare(b.equipement));
        renderEquipmentGrid(allEquipmentTypes);
        
        // Add search functionality
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filtered = allEquipmentTypes.filter(item => 
                item.equipement.toLowerCase().includes(searchTerm)
            );
            renderEquipmentGrid(filtered);
        });
    }).catch(error => {
        gridContainer.innerHTML = '<div class="error-text">Erreur de chargement</div>';
        console.error('Error loading equipment types:', error);
    });

}

/**
 * Render equipment types as selectable cards
 * @param {Array} data - Equipment types to display
 */
function renderEquipmentGrid(data) {
    const gridContainer = document.getElementById("equipmentGrid");
    
    if (data.length === 0) {
        gridContainer.innerHTML = '<div class="no-results">Aucun résultat trouvé</div>';
        return;
    }
    
    gridContainer.innerHTML = '';
    
    data.forEach(item => {
        const card = document.createElement('div');
        card.className = 'equipment-card';
        
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = 'equip-' + item.code;
        checkbox.value = item.code;
        checkbox.checked = selectedEquipmentTypes.has(item.code);
        
        checkbox.addEventListener('change', (e) => {
            if (e.target.checked) {
                selectedEquipmentTypes.add(item.code);
                card.classList.add('selected');
            } else {
                selectedEquipmentTypes.delete(item.code);
                card.classList.remove('selected');
            }
            updateSelectedCount();
        });
        
        const label = document.createElement('span');
        label.textContent = item.equipement;
        
        // Make entire card clickable
        card.addEventListener('click', (e) => {
            if (e.target === checkbox) {
                e.stopPropagation();
                return;
            }
            checkbox.checked = !checkbox.checked;
            
            // Manually trigger the selection logic
            if (checkbox.checked) {
                selectedEquipmentTypes.add(item.code);
                card.classList.add('selected');
            } else {
                selectedEquipmentTypes.delete(item.code);
                card.classList.remove('selected');
            }
            updateSelectedCount();
        });
        
        if (selectedEquipmentTypes.has(item.code)) {
            card.classList.add('selected');
        }
        
        card.appendChild(checkbox);
        card.appendChild(label);
        gridContainer.appendChild(card);
    });
    
    updateSelectedCount();
}

/**
 * Update the count of selected equipment types
 */
function updateSelectedCount() {
    const selectedCount = document.getElementById("selectedCount");
    if (selectedCount) {
        const count = selectedEquipmentTypes.size;
        if (count === 0) {
            selectedCount.textContent = 'Aucun équipement sélectionné';
        } else if (count === 1) {
            selectedCount.textContent = '1 équipement sélectionné';
        } else {
            selectedCount.textContent = count + ' équipements sélectionnés';
        }
    }
}

/**
 * Get selected equipment types
 * @returns {Array} Array of selected equipment type codes
 */
function getSelectedEquipmentTypes() {
    return Array.from(selectedEquipmentTypes);
}

function getSelected0ptions() {
    return Array.from(selectedOptions);
}

function getSelectedHandicapOptions() {
    return Array.from(selectedOptions);
}

function getSearchVille() {
    const searchInput = document.getElementById("locationSearch");
    return searchInput ? searchInput.value : '';
}

function getRayonValue(){
    const rayonInput = document.getElementById("rayon");
    return rayonInput ? parseInt(rayonInput.value) : 0;
}

function applyFilters() {
    // Reset whereClause at the start
    whereClause = '';
    
    const ville = getSearchVille();
    if(ville){
        if(whereClause === '')whereClause += `new_name ='${ville}'`;
        else whereClause += ` AND new_name='${ville}'`;
    }

    const selectedTypes = getSelectedEquipmentTypes();
    if (selectedTypes.length > 0) {
        const typesClause = selectedTypes.map(type => `equip_type_code='${type}'`).join(' OR ');
        if(whereClause === '') whereClause += `${typesClause}`;
        else whereClause += ' AND (' + typesClause + ')';
    }

    const selectedOptions = getSelected0ptions();
    if (selectedOptions.length > 0) {
        const optionsClause = selectedOptions.map(option => {
            if (option === 'sanitaires') return 'equip_sanit="true"';
            if (option === 'douche') return 'equip_douche="true"';
            return '';
        }).filter(clause => clause !== '').join(' AND ');
        
        if (optionsClause) {
            if(whereClause === '') whereClause += optionsClause;
            else whereClause += ' AND ' + optionsClause;
        }
    }

    const selectedHandicapOptions = getSelectedHandicapOptions();
    if (selectedHandicapOptions.length > 0) {
        const handicapClause = selectedHandicapOptions.map(option => {
            if (option === 'handiM') return 'equip_acces_handi_mobilite is not null';
            if (option === 'handiS') return 'equip_acces_handi_sensoriel is not null';
            return '';
        }).filter(clause => clause !== '').join(' OR ');

        if (handicapClause) {
            if(whereClause === '') whereClause += `(${handicapClause})`;
            else whereClause += ' AND (' + handicapClause + ')';
        }
    }

    const rayon = getRayonValue();
    if(rayon > 0){
        if(ville){
            alert('Vous ne pouvez pas filtrer par ville et par rayon en même temps.');
            return;
        }
        else{
            // Check if we already have user coordinates
            getUserLocation();
            if(userLatitude !== null && userLongitude !== null){
                console.log(`Using cached user coordinates: Lat ${userLatitude}, Lon ${userLongitude}`);
                const radiusMeters = rayon * 1000; // Convert km to meters
                const distanceClause = `distance(equip_coordonnees, geom'POINT(${userLongitude} ${userLatitude})', ${radiusMeters}m)`;
                
                if(whereClause === '') whereClause = distanceClause;
                else whereClause += ` AND ${distanceClause}`;
                
                console.log('Applying filters with where clause:', whereClause);
                loadmap(map, whereClause);
            } else {
                // Need to get user location first
                console.log('Getting user location...');
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            userLatitude = position.coords.latitude;
                            userLongitude = position.coords.longitude;
                            console.log(`Got user location: Lat ${userLatitude}, Lon ${userLongitude}`);
                            
                            const radiusMeters = rayon * 1000; // Convert km to meters
                            const distanceClause = `distance(equip_coordonnees, geom'POINT(${userLongitude} ${userLatitude})', ${radiusMeters}m)`;
                            
                            if(whereClause === '') whereClause = distanceClause;
                            else whereClause += ` AND ${distanceClause}`;
                            
                            console.log('Applying filters with where clause:', whereClause);
                            loadmap(map, whereClause);
                        },
                        (error) => {
                            console.error('Geolocation error:', error);
                            alert('Impossible de récupérer votre position. Assurez-vous que la géolocalisation est activée dans votre navigateur.');
                        }
                    );
                } else {
                    alert('La géolocalisation n\'est pas supportée par votre navigateur.');
                }
            }
            return; // Don't continue to loadmap() below
        }
    } 

    console.log('Applying filters with where clause:', whereClause);
    loadmap(map, whereClause);
}