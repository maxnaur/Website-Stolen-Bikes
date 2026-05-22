/*
  common/leaflet-utils.js
  Shared Leaflet helper functions used by multiple pages.
  */
window.leafletUtils = window.leafletUtils || {};
window.leafletUtils.maps = window.leafletUtils.maps || {};
window.leafletUtils.markers = window.leafletUtils.markers || {};

window.leafletUtils.geocodeAddress = async function(address) {
  if (!address) return null;

  /* within gb limit the scope of where can be searched */
  const url = `https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=gb&q=${encodeURIComponent(address)}`;
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

window.leafletUtils.reverseGeocode = async function(lat, lng) {
  const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
  try {
    const response = await fetch(url, {
      headers: { 'Accept': 'application/json' }
    });
    if (!response.ok) return null;
    const data = await response.json();
    return data.display_name || `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
  } catch (error) {
    console.error('Reverse geocode error:', error);
    return `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
  }
};

window.leafletUtils.createOrUpdateMap = function(containerId, lat, lng, zoom = 15, isDraggable = false, onDragEnd = null) {
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
      
      if (isDraggable) {
        existingMarker.dragging.enable();
      } else {
        if (existingMarker.dragging) existingMarker.dragging.disable();
      }

      existingMarker.off('dragend');
      if (isDraggable && onDragEnd) {
        existingMarker.on('dragend', onDragEnd);
      }
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

  const marker = L.marker([lat, lng], { draggable: isDraggable }).addTo(map);
  
  if (isDraggable && onDragEnd) {
    marker.on('dragend', onDragEnd);
  }

  window.leafletUtils.maps[containerId] = map;
  window.leafletUtils.markers[containerId] = marker;

  setTimeout(() => map.invalidateSize(), 100);
  return map;
};
