const mapa = L.map('map').setView([-23.667, -46.461], 11);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(mapa);
(window.profissionais || []).forEach(p => {
  if (p.latitude && p.longitude) {
    L.marker([Number(p.latitude), Number(p.longitude)]).addTo(mapa)
      .bindPopup(`<strong>${p.nome}</strong><br>${p.especialidade}<br>⭐ ${p.nota}<br><a href="/profissional/${p.id}">Ver perfil</a>`);
  }
});
document.getElementById('locateBtn')?.addEventListener('click', () => {
  if (!navigator.geolocation) return alert('Geolocalização não disponível neste navegador.');
  navigator.geolocation.getCurrentPosition(pos => {
    const lat = pos.coords.latitude, lng = pos.coords.longitude;
    mapa.setView([lat, lng], 14);
    L.circleMarker([lat, lng], {radius:8}).addTo(mapa).bindPopup('Você está aqui').openPopup();
  }, () => alert('Não foi possível acessar sua localização.'));
});
