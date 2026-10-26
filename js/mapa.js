// Mapa real com Leaflet + OpenStreetMap (gratuito, sem chave de API).
// Rota e tempo: OSRM (servidor público). Endereço: Nominatim (OpenStreetMap).

var pinoLaranja = (typeof L !== 'undefined') ? L.divIcon({ className: 'pino-laranja', iconSize: [28, 28], iconAnchor: [14, 28], popupAnchor: [0, -26] }) : null;
var mapaDiv = document.getElementById('mapa');
var mapa = null;
var meuMarcador = null;
var rotaLinha = null;
var minhaPosicao = null;
var marcadores = {};

function mostrarTexto(mensagem) {
    var texto = document.getElementById('localizacao-texto');
    if (texto) {
        texto.textContent = mensagem;
    }
}

function criarPopup(item) {
    var caixa = document.createElement('div');
    var nome = document.createElement('strong');
    nome.textContent = item.getAttribute('data-nome');
    var info = document.createElement('div');
    info.textContent = item.getAttribute('data-info');
    var link = document.createElement('a');
    link.href = 'profissional.php?id=' + item.getAttribute('data-id');
    link.textContent = 'Ver perfil';
    caixa.appendChild(nome);
    caixa.appendChild(info);
    caixa.appendChild(link);
    return caixa;
}

function iniciarMapa() {
    if (!mapaDiv) {
        return;
    }

    if (typeof L === 'undefined') {
        mapaDiv.textContent = 'Não foi possível carregar o mapa. Verifique sua conexão com a internet.';
        return;
    }

    mapa = L.map('mapa').setView([-23.67, -46.5], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; contribuidores do <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(mapa);

    var pontos = [];

    document.querySelectorAll('.localizacao-item').forEach(function (item) {
        var lat = parseFloat(item.getAttribute('data-lat'));
        var lon = parseFloat(item.getAttribute('data-lon'));

        if (isNaN(lat) || isNaN(lon)) {
            return;
        }

        var marcador = L.marker([lat, lon], { icon: pinoLaranja }).addTo(mapa);
        marcador.bindPopup(criarPopup(item));
        marcadores[item.getAttribute('data-id')] = marcador;
        pontos.push([lat, lon]);
    });

    if (pontos.length > 0) {
        mapa.fitBounds(pontos, { padding: [40, 40] });
    }

    // Mapa de um único profissional (página do perfil)
    if (mapaDiv.getAttribute('data-unico')) {
        mapa.setZoom(14);
    }
}

function pedirLocalizacao(depois) {
    if (!navigator.geolocation) {
        mostrarTexto('Seu navegador não permite usar localização.');
        return;
    }

    mostrarTexto('Buscando sua localização...');

    navigator.geolocation.getCurrentPosition(
        function (posicao) {
            minhaPosicao = [posicao.coords.latitude, posicao.coords.longitude];

            if (mapa) {
                if (meuMarcador) {
                    mapa.removeLayer(meuMarcador);
                }
                meuMarcador = L.circleMarker(minhaPosicao, {
                    radius: 9, color: '#ffffff', weight: 3, fillColor: '#111114', fillOpacity: 1
                }).addTo(mapa).bindPopup('Você está aqui');
            }

            document.querySelectorAll('.localizacao-item').forEach(function (item) {
                var distancia = calcularDistancia(minhaPosicao[0], minhaPosicao[1],
                    parseFloat(item.getAttribute('data-lat')), parseFloat(item.getAttribute('data-lon')));
                item.setAttribute('data-distancia', distancia);
                item.querySelector('.distancia').textContent = distancia.toFixed(1) + ' km em linha reta';
            });

            ordenarPorDistancia();
            mostrarTexto('Lista organizada pela distância até você.');

            if (depois) {
                depois();
            }
        },
        function () {
            mostrarTexto('Não foi possível acessar sua localização. Libere a permissão no navegador.');
        },
        { enableHighAccuracy: true, timeout: 15000 }
    );
}

// Traça a rota de carro até o profissional e mostra distância e tempo (como nos apps de transporte)
function tracarRota(item) {
    var destino = [parseFloat(item.getAttribute('data-lat')), parseFloat(item.getAttribute('data-lon'))];
    var info = item.querySelector('.rota-info');

    info.textContent = 'Calculando rota...';

    var url = 'https://router.project-osrm.org/route/v1/driving/' +
        minhaPosicao[1] + ',' + minhaPosicao[0] + ';' + destino[1] + ',' + destino[0] +
        '?overview=full&geometries=geojson';

    fetch(url)
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (!dados.routes || dados.routes.length === 0) {
                info.textContent = 'Não encontramos rota.';
                return;
            }

            var rota = dados.routes[0];
            var km = (rota.distance / 1000).toFixed(1).replace('.', ',');
            var minutos = Math.max(1, Math.round(rota.duration / 60));

            if (rotaLinha) {
                mapa.removeLayer(rotaLinha);
            }

            rotaLinha = L.geoJSON(rota.geometry, { style: { color: '#111114', weight: 5 } }).addTo(mapa);
            mapa.fitBounds(rotaLinha.getBounds(), { padding: [40, 40] });

            info.textContent = km + ' km · cerca de ' + minutos + ' min de carro';
        })
        .catch(function () {
            info.textContent = 'Não foi possível calcular a rota agora.';
        });
}

var botaoLoc = document.getElementById('usar-localizacao');

if (botaoLoc) {
    botaoLoc.addEventListener('click', function () {
        pedirLocalizacao(null);
    });
}

document.querySelectorAll('.botao-rota').forEach(function (botao) {
    botao.addEventListener('click', function () {
        var item = botao.closest('.localizacao-item');

        if (marcadores[item.getAttribute('data-id')]) {
            marcadores[item.getAttribute('data-id')].openPopup();
        }

        if (minhaPosicao) {
            tracarRota(item);
        } else {
            pedirLocalizacao(function () { tracarRota(item); });
        }
    });
});

iniciarMapa();
