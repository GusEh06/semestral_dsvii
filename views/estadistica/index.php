<?php include 'views/layouts/header.php'; ?>

<h1 class="mt-4 mb-4">Estadísticas de Colaboradores</h1>

<div class="row">
    <!-- Primera columna -->
    <div class="col-md-6">
        <!-- Estadísticas por Sexo -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Colaboradores por Sexo
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center mb-3">
                    <canvas id="chartSexo" style="max-width: 100%; height: 300px;"></canvas>
                </div>
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Sexo</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($porSexo as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['sexo']); ?></td>
                                <td><?= (int)$fila['total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Estadísticas por Dirección -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Colaboradores por Dirección
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center mb-3">
                    <canvas id="chartDireccion" style="max-width: 100%; height: 300px;"></canvas>
                </div>
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Dirección</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($colaboradoresDireccion as $dato): ?>
                            <tr>
                                <td><?= htmlspecialchars($dato['direccion']); ?></td>
                                <td><?= (int)$dato['total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Segunda columna -->
    <div class="col-md-6">
        <!-- Estadísticas por Rango de Edad -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Colaboradores por Rango de Edad
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center mb-3">
                    <canvas id="chartEdad" style="max-width: 100%; height: 300px;"></canvas>
                </div>
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Rango de Edad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($porEdad as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['rango_edad']); ?></td>
                                <td><?= (int)$fila['total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Datos para gráfico Sexo
const sexoLabels = <?= json_encode(array_column($porSexo, 'sexo')); ?>;
const sexoData = <?= json_encode(array_column($porSexo, 'total')); ?>;

// Datos para gráfico Edad
const edadLabels = <?= json_encode(array_column($porEdad, 'rango_edad')); ?>;
const edadData = <?= json_encode(array_column($porEdad, 'total')); ?>;

// Datos para gráfico Dirección
const direccionLabels = <?= json_encode(array_column($colaboradoresDireccion, 'direccion')); ?>;
const direccionData = <?= json_encode(array_column($colaboradoresDireccion, 'total')); ?>;

function crearGrafico(ctx, labels, data, label, colores) {
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: colores,
                borderColor: colores.map(c => c.replace('0.6', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, precision:0 }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

const coloresSexo = ['rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(255, 206, 86, 0.6)'];
const coloresEdad = ['rgba(153, 102, 255, 0.6)', 'rgba(75, 192, 192, 0.6)', 'rgba(255, 159, 64, 0.6)', 'rgba(201, 203, 207, 0.6)'];
const coloresDireccion = ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)', 'rgba(153, 102, 255, 0.6)'];

crearGrafico(document.getElementById('chartSexo').getContext('2d'), sexoLabels, sexoData, 'Total por Sexo', coloresSexo);
crearGrafico(document.getElementById('chartEdad').getContext('2d'), edadLabels, edadData, 'Total por Rango de Edad', coloresEdad);
crearGrafico(document.getElementById('chartDireccion').getContext('2d'), direccionLabels, direccionData, 'Total por Dirección', coloresDireccion);

<?php if (!empty($_SESSION['mensaje'])): ?>
Swal.fire({
    icon: '<?= $_SESSION['mensaje']['tipo']; ?>',
    title: '<?= $_SESSION['mensaje']['tipo'] === 'success' ? 'Éxito' : 'Error'; ?>',
    text: '<?= $_SESSION['mensaje']['texto']; ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php unset($_SESSION['mensaje']); endif; ?>
</script>

<?php include 'views/layouts/footer.php'; ?>
