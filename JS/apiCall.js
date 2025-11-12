/**
 * Calls the French sports facilities API
 * @param {Object} options - Query options
 * @param {number} options.limit - Maximum number of records to return (default: 10)
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
    
    if (options.limit) params.append('limit', options.limit);
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
        return data;
    } catch (error) {
        console.error('Error fetching sports equipments:', error);
        throw error;
    }
}

getSportsEquipments();