var inputMatricula = document.querySelector('#buscador');
inputMatricula.addEventListener('input', borrarMatricula);

function borrarMatricula() {
    // Verificar si el contenido está vacío
    if (inputMatricula.value === '') {
        // Envía el formulario de búsqueda
        document.querySelector('form').submit();
    }
}
const $btnExportar = document.querySelector("#btnExportar"),
$tabla = document.querySelector("#tabla");

$btnExportar.addEventListener("click", function () {
const datos = [];
const headers = [];
const bodyRows = [];

// Tomar los headers de la tabla
const $headers = $tabla.querySelectorAll("thead th");
$headers.forEach((th) => headers.push(th.textContent));

// Tomar las filas de las tablas
const $rows = $tabla.querySelectorAll("tbody tr");
$rows.forEach((row) => {
    const rowData = [];
    const $cells = row.querySelectorAll("td");
    $cells.forEach((cell) => rowData.push(cell.textContent));
    bodyRows.push(rowData);
});

// Agregar los datos a Excel
const groupedData = <?php <?php echo json_encode($groupedData); ?>;
Object.entries(groupedData).forEach(([servicio, cantidad]) => {
    bodyRows.push([servicio, cantidad]);
});

// Combinar los headers y filas
datos.push(headers);
datos.push(...bodyRows);

// Exportar en Excel
const worksheet = XLSX.utils.aoa_to_sheet(datos);
const workbook = XLSX.utils.book_new();
XLSX.utils.book_append_sheet(workbook, worksheet, "Reporte de registros");
const wbout = XLSX.write(workbook, { bookType: "xlsx", type: "array" });
saveAs(new Blob([wbout], { type: "application/octet-stream" }), "Reporte_de_registros.xlsx");
});

document.addEventListener('DOMContentLoaded', function(){
//Sleccionar los elementos
const inputBuscador = document.querySelector('#buscador');
inputBuscador.addEventListener('blur', validar);

function validar(e){

if (e.target.value.trim() === '') {
    mostrarAlerta(`El campo ${e.target.id} es obligatorio`, e.target.parentElement);
    return;
} limpiarAlerta(e.target.parentElement);
}
function mostrarAlerta(mensaje, referencia) {
limpiarAlerta(referencia);
const error = document.createElement('P');
error.textContent = mensaje;
error.classList.add('bg-red-600', 'text-red-500', 'p-2', 'text-center');
referencia.appendChild(error);
}

function limpiarAlerta(referencia){
const alerta = referencia.querySelector('.bg-red-600');
if (alerta) {
    alerta.remove();
}
console.log('desde limpiar alerta');
}
});