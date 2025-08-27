<div>
    {{-- Styles --}}
   @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css">
@endpush


    {{-- Budget Summary --}}
    {{-- <div class="alert alert-info text-center fw-bold">
        إجمالي الميزانية: <br>
        <span class="fs-4">{{ number_format($totalBudget, 2) }} س.ر</span>
    </div> --}}

    <div class="row">
        {{-- Left Column: Add New Item --}}
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    إضافة بند جديد
                </div>
                <div class="card-body">
                    @include('admin.budgets.partials.create', ['allItems' => $allItems])
                </div>
            </div>
        </div>

        {{-- Right Column: Item Tree --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    شجرة البنود
                </div>
                <div class="card-body">
                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            @foreach ($items as $item)
                                @include('admin.budgets.partials.item', ['item' => $item])
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        {{-- Nestable JS --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>

        {{-- TomSelect JS --}}
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        {{-- Nestable + TomSelect Init --}}
        <script>
            $(document).ready(function() {
                $('#nestable').nestable();

                $('#nestable').on('change', function() {
                    const data = $('#nestable').nestable('serialize');

                    $.post('{{ route('budgets.reorder') }}', {
                        _token: '{{ csrf_token() }}',
                        data: data
                    });
                });
            });
        </script>

        <script>
            $(document).on('click', '.delete-item', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                if (!confirm('هل أنت متأكد من حذف هذا البند؟')) return;

                $.ajax({
                    url: `/budgets/destroy/${id}`,
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function () {
                        $(`li[data-id="${id}"]`).fadeOut(300, function () {
                            $(this).remove();
                        });
                    },
                    error: function (xhr) {
                        alert('حدث خطأ أثناء الحذف');
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>
    @endpush
</div>
