// Keep the event venue choices in sync with the county selected in the form.
function initVenueFilter() {
    const regionSelect = document.querySelector('[data-venue-region]');
    const venueSelect = document.querySelector('[data-venue-select]');
    const hint = document.querySelector('[data-venue-hint]');
    if (!regionSelect || !venueSelect || !hint) return;

    let venues;
    try {
        venues = JSON.parse(venueSelect.dataset.venues);
        if (!Array.isArray(venues)) throw new TypeError('Venue data must be an array.');
    } catch (error) {
        console.error('Unable to read the event venue list.', error);
        window.showToast?.('Venue options could not be loaded. Please reload the form.', 'error');
        return;
    }

    const originalVenueId = venueSelect.value;
    const originalVenue = venues.find((venue) => String(venue.id) === originalVenueId);
    if (!regionSelect.value && originalVenue) {
        regionSelect.value = String(originalVenue.region_id);
    }

    function updateVenues(selectedVenueId = '') {
        const regionId = regionSelect.value;
        const matchingVenues = regionId
            ? venues.filter((venue) => String(venue.region_id) === regionId)
            : venues;

        venueSelect.replaceChildren(new Option('Select venue', ''));
        matchingVenues.forEach((venue) => {
            const option = new Option(`${venue.name} - ${venue.region_name}`, String(venue.id));
            venueSelect.add(option);
        });

        const selectedVenue = matchingVenues.find((venue) => String(venue.id) === selectedVenueId);
        venueSelect.value = selectedVenue ? String(selectedVenue.id) : '';

        const noVenuesForCounty = regionId && matchingVenues.length === 0;
        const noVenuesAtAll = !regionId && venues.length === 0;
        hint.hidden = !noVenuesForCounty && !noVenuesAtAll;
        if (noVenuesForCounty) {
            const selectedCounty = regionSelect.options[regionSelect.selectedIndex]?.textContent || 'this county';
            hint.textContent = `No venues are available in ${selectedCounty}. Choose another county or add a venue first.`;
        } else if (noVenuesAtAll) {
            hint.textContent = 'No venues are available yet. Add a venue before creating an event.';
        } else {
            hint.textContent = '';
        }
    }

    regionSelect.addEventListener('change', () => updateVenues());
    updateVenues(originalVenueId);
}

document.addEventListener('DOMContentLoaded', initVenueFilter);