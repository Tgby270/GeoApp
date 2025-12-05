/**
 * Function to open the filters modal
 */
function openFilters() {
  const modal = document.getElementById('filters_modal');
  modal.style.display = 'flex';
  modal.style.alignItems = 'center';
  modal.style.justifyContent = 'center';
}

/**
 * Function to close the filters modal
 */
function closeFilters() {
  const modal = document.getElementById('filters_modal');
  modal.style.display = 'none';
}

openF = document.getElementById("openFilters");
closeF = document.getElementById("closeFilters");

let appliedFilters = document.getElementById("applyFiltersBtn");

appliedFilters.addEventListener("click", function() {
  closeFilters();
  // Here you can add code to actually apply the filters
});

openF.addEventListener("click", function() {
  openFilters();
});

closeF.addEventListener("click", function() {
  closeFilters();
});

// Close modal when clicking outside the content
window.addEventListener('click', function(event) {
    const modal = document.getElementById('filters_modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});