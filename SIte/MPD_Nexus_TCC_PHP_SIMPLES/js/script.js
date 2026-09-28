// Menu para celular
var botaoMenu = document.getElementById('botao-menu');
var menu = document.getElementById('menu');

if (botaoMenu && menu) {
    botaoMenu.addEventListener('click', function () {
        menu.classList.toggle('aberto');
    });
}

// Localização do usuário
var botaoLocalizacao = document.getElementById('usar-localizacao');

if (botaoLocalizacao) {
    botaoLocalizacao.addEventListener('click', function () {
        var texto = document.getElementById('localizacao-texto');

        if (!navigator.geolocation) {
            texto.textContent = 'Seu navegador não permite usar localização.';
            return;
        }

        texto.textContent = 'Buscando sua localização...';

        navigator.geolocation.getCurrentPosition(
            function (posicao) {
                var minhaLat = posicao.coords.latitude;
                var minhaLon = posicao.coords.longitude;
                var itens = document.querySelectorAll('.localizacao-item');

                itens.forEach(function (item) {
                    var lat = parseFloat(item.getAttribute('data-lat'));
                    var lon = parseFloat(item.getAttribute('data-lon'));
                    var distancia = calcularDistancia(minhaLat, minhaLon, lat, lon);

                    item.setAttribute('data-distancia', distancia);
                    item.querySelector('.distancia').textContent = distancia.toFixed(1) + ' km aproximadamente';
                });

                ordenarPorDistancia();
                texto.textContent = 'Lista organizada pela distância aproximada.';
            },
            function () {
                texto.textContent = 'Não foi possível acessar sua localização.';
            }
        );
    });
}

function calcularDistancia(lat1, lon1, lat2, lon2) {
    var raioTerra = 6371;
    var diferencaLat = grausParaRadianos(lat2 - lat1);
    var diferencaLon = grausParaRadianos(lon2 - lon1);

    var a = Math.sin(diferencaLat / 2) * Math.sin(diferencaLat / 2) +
        Math.cos(grausParaRadianos(lat1)) *
        Math.cos(grausParaRadianos(lat2)) *
        Math.sin(diferencaLon / 2) * Math.sin(diferencaLon / 2);

    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return raioTerra * c;
}

function grausParaRadianos(valor) {
    return valor * Math.PI / 180;
}

function ordenarPorDistancia() {
    var lista = document.getElementById('lista-localizacao');

    if (!lista) {
        return;
    }

    var itens = Array.from(lista.querySelectorAll('.localizacao-item'));

    itens.sort(function (a, b) {
        return parseFloat(a.getAttribute('data-distancia')) - parseFloat(b.getAttribute('data-distancia'));
    });

    itens.forEach(function (item) {
        lista.appendChild(item);
    });
}
