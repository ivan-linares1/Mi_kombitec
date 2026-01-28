<style>
/* Modal más pequeño */
#stockModal .modal-dialog {
    width: 35%;
}

/* HEADER con color principal */
#stockModal .modal-header {
    background-color: #05564f;
    padding: 0.75rem 1rem;
    border-bottom: 2px solid #ff6a00;
}

/* Título */
#stockModal .modal-title {
    color: #ffffff;
    font-size: 0.95rem;
}

/* Botón cerrar en blanco */
#stockModal .btn-close {
    filter: invert(1);
    opacity: 0.9;
}

/* Body compacto */
#stockModal .modal-body {
    padding: 1rem;
}

/* Footer compacto */
#stockModal .modal-footer {
    padding: 0.75rem 1rem;
}

/* Labels */
#stockModal .form-label {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
    color: #05564f;
}

/* Inputs pequeños */
#stockModal .form-control,
#stockModal .select2-container--default .select2-selection--single {
    height: 34px;
    font-size: 0.85rem;
    border: 1px solid #ced4da;
}

/* FOCUS y HOVER inputs */
#stockModal .form-control:focus,
#stockModal .select2-container--default .select2-selection--single:focus {
    border-color: #ff6a00;
    box-shadow: 0 0 0 0.1rem rgba(255, 106, 0, 0.25);
}

/* Select2 texto */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 32px;
    font-size: 0.85rem;
}

/* Botón principal */
#stockModal .btn-modern,
#stockModal .btn-primary {
    background-color: #05564f;
    border-color: #05564f;
    font-size: 0.8rem;
    padding: 0.3rem 0.8rem;
}

/* Hover botón */
#stockModal .btn-modern:hover,
#stockModal .btn-primary:hover {
    background-color: #ff6a00;
    border-color: #ff6a00;
}

/* Botón secundario */
#stockModal .btn-secondary {
    font-size: 0.8rem;
}

/* SweetAlert más pequeño */
.swal2-popup {
    width: 300px !important;
    padding: 0.8rem !important;
    border-radius: 12px;
}

/* Título SweetAlert */
.swal2-title {
    font-size: 0.95rem !important;
}

/* Botón SweetAlert */
.swal2-confirm {
    background-color: #05564f !important;
}

.swal2-confirm:hover {
    background-color: #ff6a00 !important;
}
</style>


<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title fw-bold title-modern" id="stockModalLabel">Consulta de Stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">

        <div class="mb-3">
            <label class="form-label fw-semibold">Artículo</label>
            <select id="selectArticulo" class="form-control select2"></select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Cantidad</label>
            <input type="number" id="inputCantidad" class="form-control">
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button id="btnConsultar" class="btn btn-primary btn-modern">Consultar</button>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {

    function initSelect2() {
    const $select = $('#selectArticulo');

    if (!window.jQuery || !$.fn.select2) {
        return;
    }

    // 🔥 SI YA EXISTE, DESTRÚYELO
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
        $select.empty(); // limpia opciones viejas
    }

    // 🔥 CREA DE NUEVO (siempre limpio)
    $select.select2({
        placeholder: "Selecciona un artículo",
        width: '100%',
        allowClear: true,
        dropdownParent: $('#stockModal'),
        ajax: {
            url: "{{ route('consulta_stock.buscar') }}",
            dataType: 'json',
            delay: 250,
            data(params) {
                return {
                    search: params.term || '',
                    page: params.page || 1
                };
            },
            processResults(data) {
                return {
                    results: data.results,
                    pagination: { more: data.pagination.more }
                };
            },
            cache: true
        }
    });
}

    // 🔥 AL ABRIR MODAL
    $('#stockModal').on('shown.bs.modal', function () {
        $('#inputCantidad').val('').focus();
        initSelect2();
    });

    // ENTER
    $('#inputCantidad').on('keypress', function (e) {
        if (e.which === 13) {
            $('#btnConsultar').click();
        }
    });

    // ---------- CONSULTAR ----------
    $('#btnConsultar').on('click', function () {
        const itemCode = $('#selectArticulo').val();
        const cantidad = $('#inputCantidad').val();

        if (!itemCode) {
            Swal.fire({ icon: 'error', title: "Selecciona un artículo primero." });
            return;
        }

        if (!cantidad || cantidad <= 0) {
            Swal.fire({ icon: 'error', title: "Ingresa una cantidad válida." });
            return;
        }

        $.ajax({
            url: "{{ route('consulta_stock.ver') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                itemCode,
                cantidad
            },
            success(res) {
                Swal.fire({
                    icon: res.success ? 'success' : 'error',
                    title: res.success
                        ? 'Stock disponible'
                        : 'Sin stock suficiente',
                    width: 250,
                    padding: '0.7rem',
                    showConfirmButton: false,
                    timer: 1600,
                    customClass: {
                        popup: 'rounded-3',
                        title: 'fs-6'
                    }
                });
            },
            error() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al consultar',
                    text: 'No se pudo verificar el stock',
                    width: 250,
                    padding: '0.7rem',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-3',
                        title: 'fs-6'
                    }
                });
            }
        });
    });

})();
</script>
@endpush

