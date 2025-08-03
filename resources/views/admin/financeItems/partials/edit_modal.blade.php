<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.finance-items.update', $item->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $item->id }}">تعديل: {{ $item->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم البند</label>
                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المبلغ</label>
                        <input type="number" name="amount" class="form-control" step="0.01" value="{{ $item->amount }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">البند الأب (اختياري)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">— لا شيء —</option>
                            @foreach ($allItems as $opt)
                                @if ($opt->id !== $item->id)
                                    <option value="{{ $opt->id }}" {{ $item->parent_id == $opt->id ? 'selected' : '' }}>
                                        {{ $opt->name }} ({{ $opt->type }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </div>
        </form>
    </div>
</div>
