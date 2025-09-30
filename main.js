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
