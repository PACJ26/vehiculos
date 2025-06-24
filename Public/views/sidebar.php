<!-- Menú lateral para todas las páginas del sistema -->
<nav class="col-md-2 text-white sidebar py-3" style="background-color: var(--primary-color); min-height: 100vh; height: 100%; position: sticky; top: 0; overflow-y: auto;">
    <div class="sidebar-sticky">
    <h3 class="text-center py-2 mb-3" style="border-bottom: 1px solid rgba(255,255,255,0.2);">Módulo Vehículos</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white fw-medium <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white fw-medium <?php echo basename($_SERVER['PHP_SELF']) === 'propietarios.php' ? 'active' : ''; ?>" href="propietarios.php">
                <i class="fas fa-users me-2"></i> Propietarios
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white fw-medium <?php echo basename($_SERVER['PHP_SELF']) === 'vehiculos.php' ? 'active' : ''; ?>" href="vehiculos.php">
                <i class="fas fa-car me-2"></i> Vehículos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white fw-medium <?php echo basename($_SERVER['PHP_SELF']) === 'seguros.php' ? 'active' : ''; ?>" href="seguros.php">
                <i class="fas fa-file-contract me-2"></i> Seguros
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white fw-medium <?php echo basename($_SERVER['PHP_SELF']) === 'multas.php' ? 'active' : ''; ?>" href="multas.php">
                <i class="fas fa-exclamation-triangle me-2"></i> Multas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white fw-medium <?php echo basename($_SERVER['PHP_SELF']) === 'mantenimiento.php' ? 'active' : ''; ?>" href="mantenimiento.php">
                <i class="fas fa-tools me-2"></i> Mantenimiento
            </a>
        </li>
    </ul>
    </div>
</nav>