<?php
class Paginacion
{
    private $pagina;
    private $cantidadPorPagina;
    private $totalItems;

    public function __construct($pagina, $cantidadPorPagina, $totalItems)
    {
        $this->pagina = $pagina;
        $this->cantidadPorPagina = $cantidadPorPagina;
        $this->totalItems = $totalItems;
    }

    public function generarLinks()
    {
        $totalPaginas = ceil($this->totalItems / $this->cantidadPorPagina);
        $links = [];

        for ($i = 1; $i <= $totalPaginas; $i++) {
            $links[] = "<a href='?pagina=$i'>$i</a>";
        }

        return implode(' ', $links);
    }
}
?>