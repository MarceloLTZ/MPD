// Menu para celular
var botaoMenu = document.getElementById('botao-menu');
var menu = document.getElementById('menu');

if (botaoMenu && menu) {
    botaoMenu.addEventListener('click', function () {
        menu.classList.toggle('aberto');
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

// Confirmar senha (cadastro): avisa antes de enviar se as senhas forem diferentes
var formCadastro = document.getElementById('form-cadastro');

if (formCadastro) {
    formCadastro.addEventListener('submit', function (evento) {
        var senha = document.getElementById('senha').value;
        var confirmar = document.getElementById('confirmar_senha').value;

        if (senha !== confirmar) {
            evento.preventDefault();
            alert('As senhas não são iguais.');
        }
    });
}

// "Mostrar senha": troca os campos de senha entre ocultos e visíveis
document.querySelectorAll('[data-mostrar-senha]').forEach(function (caixa) {
    caixa.addEventListener('change', function () {
        var formulario = caixa.closest('form');

        formulario.querySelectorAll('input[name="senha"], input[name="confirmar_senha"]').forEach(function (campo) {
            campo.type = caixa.checked ? 'text' : 'password';
        });
    });
});

// Solicitar serviço: preenche o endereço com a localização atual (OpenStreetMap / Nominatim)
var botaoEndereco = document.getElementById('usar-endereco-atual');

if (botaoEndereco) {
    botaoEndereco.addEventListener('click', function () {
        var campo = document.getElementById('endereco');
        var aviso = document.getElementById('endereco-aviso');

        if (!navigator.geolocation) {
            aviso.textContent = 'Seu navegador não permite usar localização.';
            return;
        }

        aviso.textContent = 'Buscando sua localização...';

        navigator.geolocation.getCurrentPosition(function (posicao) {
            var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=pt-BR&lat=' +
                posicao.coords.latitude + '&lon=' + posicao.coords.longitude;

            fetch(url)
                .then(function (resposta) { return resposta.json(); })
                .then(function (dados) {
                    campo.value = dados.display_name || '';
                    aviso.textContent = 'Endereço preenchido. Confira e ajuste o número, se precisar.';
                })
                .catch(function () {
                    aviso.textContent = 'Não foi possível descobrir o endereço. Digite manualmente.';
                });
        }, function () {
            aviso.textContent = 'Não foi possível acessar sua localização. Libere a permissão no navegador.';
        });
    });
}
