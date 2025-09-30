// Función para inicializar el formulario de búsqueda de direcciones
function initBuscarDireccion() {
    const form = document.querySelector('form[action="buscar_direccion.php"]');
    const resultados = document.getElementById('resultados');
    if (!form || !resultados) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const direccion = form.direccion.value.trim();
        if (direccion === '') {
            resultados.innerHTML = '<p>Ingrese una dirección para buscar.</p>';
            return;
        }
        resultados.innerHTML = '<p>Buscando...</p>';
        fetch('buscar_direccion.php?direccion=' + encodeURIComponent(direccion))
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
