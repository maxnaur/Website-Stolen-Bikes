/**
 * common/leaflet-utils.js
 * Shared Leaflet helper functions used by multiple pages.
 */
window.leafletUtils = window.leafletUtils || {};
window.leafletUtils.maps = window.leafletUtils.maps || {};
window.leafletUtils.markers = window.leafletUtils.markers || {};

window.leafletUtils.geocodeAddress = async function(address) {
  if (!address) return null;

  const url = `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(address)}`;
  const response = await fetch(url, {
    headers: {
      'Accept': 'application/json'
    }
  });

  if (!response.ok) {
    return null;
  }

  const data = await response.json();
  if (!Array.isArray(data) || data.length === 0) {
    return null;
  }

  return {
    lat: parseFloat(data[0].lat),
    lng: parseFloat(data[0].lon),
    display_name: data[0].display_name
  };
};

window.leafletUtils.createOrUpdateMap = function(containerId, lat, lng, zoom = 15) {
  if (!window.L) {
    throw new Error('Leaflet must be loaded before using leafletUtils.');
  }

  const container = document.getElementById(containerId);
  if (!container) {
    return null;
  }

  if (window.leafletUtils.maps[containerId]) {
    const existingMap = window.leafletUtils.maps[containerId];
    existingMap.setView([lat, lng], zoom);
    const existingMarker = window.leafletUtils.markers[containerId];
    if (existingMarker) {
      existingMarker.setLatLng([lat, lng]);
    }
    setTimeout(() => existingMap.invalidateSize(), 100);
    return existingMap;
  }

  const map = L.map(containerId, {
    attributionControl: false
  }).setView([lat, lng], zoom);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  const marker = L.marker([lat, lng]).addTo(map);
  window.leafletUtils.maps[containerId] = map;
  window.leafletUtils.markers[containerId] = marker;

  setTimeout(() => map.invalidateSize(), 100);
  return map;
};
