// Función para inicializar el formulario de búsqueda de direcciones
function initBuscarDireccion() {
    const form = document.querySelector('form[action="buscar_direccion.php"]');
    const resultados = document.getElementById('resultados');
    if (!form || !resultados) return;
    if (form.dataset.initialized === '1') return; // evitar doble inicialización
    form.dataset.initialized = '1';
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Nuevos campos del formulario
        const calle = (form.calle ? form.calle.value.trim() : '');
        const codigoZona = (form.codigoZonaVenta ? form.codigoZonaVenta.value.trim() : '');
    const mz = (form.mz ? form.mz.value.trim() : '');
    const lt = (form.lt ? form.lt.value.trim() : '');
    const numeral = (form.numeral ? form.numeral.value.trim() : '');
        const sector = (form.sector ? form.sector.value.trim() : '');
        const grupo = (form.grupo ? form.grupo.value.trim() : '');
        const puesto = (form.puesto ? form.puesto.value.trim() : '');
        if (calle === '') {
            resultados.innerHTML = '<p>Ingrese una dirección para buscar.</p>';
            return;
        }
        resultados.innerHTML = '<p>Buscando...</p>';
        // Construir parámetros incluyendo el código de zona si se proporcionó
        const params = new URLSearchParams();
        params.set('calle', calle);
        if (codigoZona !== '') {
            params.set('codigoZonaVenta', codigoZona);
        }
        if (mz !== '') params.set('mz', mz);
        if (lt !== '') params.set('lt', lt);
    if (numeral !== '') params.set('numeral', numeral);
        if (sector !== '') params.set('sector', sector);
        if (grupo !== '') params.set('grupo', grupo);
        if (puesto !== '') params.set('puesto', puesto);
        fetch('buscar_direccion.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                resultados.innerHTML = html;
            })
            .catch(() => {
                resultados.innerHTML = '<p>Error al buscar la dirección.</p>';
            });
    });
}

// Inicializar el módulo si se carga el formulario de direcciones
if (window.location.pathname.endsWith('home.html')) {
    document.addEventListener('DOMContentLoaded', function() {
        // Cuando se carga el módulo de direcciones desde home.html
        const mainContent = document.getElementById('mainContent');
        const observer = new MutationObserver(() => {
            if (document.querySelector('form[action="buscar_direccion.php"]')) {
                initBuscarDireccion();
            }
        });
        observer.observe(mainContent, { childList: true, subtree: true });
    });
}
function initSubirClientes() {
    var uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.onsubmit = function(e) {
            e.preventDefault();
            var formData = new FormData();
            var fileInput = document.getElementById('excelFile');
            var loader = document.getElementById('loader');
            var result = document.getElementById('result');
            result.innerText = '';
            if (fileInput.files.length === 0) {
                result.innerText = 'Selecciona un archivo.';
                return;
            }
            loader.style.display = 'block';
            formData.append('excelFile', fileInput.files[0]);
            fetch('upload_excel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                loader.style.display = 'none';
                result.innerText = data;
            })
            .catch(error => {
                loader.style.display = 'none';
                result.innerText = 'Error al subir el archivo.';
            });
        };
    }
}
window.initSubirClientes = initSubirClientes;
function initBuscadorClientes() {
    var buscarBtn = document.getElementById('buscarBtn');
    if (buscarBtn) {
        buscarBtn.onclick = function() {
            const codigo = document.getElementById('codigo').value.trim();
            const dni = document.getElementById('dni').value.trim();
            const nombres = document.getElementById('nombres').value.trim();
            const resultadosBox = document.getElementById('resultadosBox');
            resultadosBox.innerHTML = '<span style="color:#888;">Buscando...</span>';
            fetch(`consultar_clientes.php?codigo=${encodeURIComponent(codigo)}&dni=${encodeURIComponent(dni)}&nombres=${encodeURIComponent(nombres)}`)
                .then(response => response.text())
                .then(html => {
                    resultadosBox.innerHTML = html;
                })
                .catch(() => {
                    resultadosBox.innerHTML = '<span style="color:red;">Error al consultar.</span>';
                });
        };
    }
}
window.initBuscadorClientes = initBuscadorClientes;
// Buscador de clientes
document.addEventListener('DOMContentLoaded', function() {
    var buscarBtn = document.getElementById('buscarBtn');
    if (buscarBtn) {
        buscarBtn.addEventListener('click', function() {
            const codigo = document.getElementById('codigo').value.trim();
            const dni = document.getElementById('dni').value.trim();
            const nombres = document.getElementById('nombres').value.trim();
            const resultadosBox = document.getElementById('resultadosBox');
            resultadosBox.innerHTML = '<span style="color:#888;">Buscando...</span>';
            fetch(`consultar_clientes.php?codigo=${encodeURIComponent(codigo)}&dni=${encodeURIComponent(dni)}&nombres=${encodeURIComponent(nombres)}`)
                .then(response => response.text())
                .then(html => {
                    resultadosBox.innerHTML = html;
                })
                .catch(() => {
                    resultadosBox.innerHTML = '<span style="color:red;">Error al consultar.</span>';
                });
        });
    }
});
// Animación CSS para el loader ya está en styles.css

document.addEventListener('DOMContentLoaded', function() {
    var uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData();
            var fileInput = document.getElementById('excelFile');
            var loader = document.getElementById('loader');
            var result = document.getElementById('result');
            result.innerText = '';
            if (fileInput.files.length === 0) {
                result.innerText = 'Selecciona un archivo.';
                return;
            }
            loader.style.display = 'block';
            formData.append('excelFile', fileInput.files[0]);
            fetch('upload_excel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                loader.style.display = 'none';
                result.innerText = data;
            })
            .catch(error => {
                loader.style.display = 'none';
                result.innerText = 'Error al subir el archivo.';
            });
        });
    }
});
