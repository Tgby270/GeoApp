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
    const limit = 100; // API max limit
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

    // Use for...of to iterate over array values
    markers = new L.MarkerClusterGroup({
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
            if(facility.arr_name){
                str += facility.arr_name;
            }

            marker.bindPopup(str);
        } else {
            noCoordCount++;
        }
    }
    markers.addTo(map);
    console.log('Number of facilities without coordinates:', noCoordCount);
}

/**
 * Get user's current location
 */
function getLocation() {
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
    console.log("Latitude: " + position.coords.latitude + " Longitude: " + position.coords.longitude);
    // Optionally center map on user location
    if (typeof map !== 'undefined') {
        map.setView([position.coords.latitude, position.coords.longitude], 13);
    }
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
 */
function loadmap(map) {
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution:
            '&copy; <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>'
    }).addTo(map);


    getAllSportsEquipments(3000, {where: "arr_name='Caen'"})
        .then(data => {
            console.log('Sports facilities data:', data.length, 'records');
            createMarkers(data);
            const loading = document.getElementById('loading');
            if (loading) loading.classList.add('hidden');
        })
        .catch(error => console.error('Error:', error));
}