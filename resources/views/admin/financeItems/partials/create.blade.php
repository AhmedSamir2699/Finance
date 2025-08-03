<form method="POST" action="{{ route('finance-items.store') }}" class="p-4 mt-4 bg-white rounded shadow-sm w-100" style="max-width: 500px;">
    @csrf

    {{-- <div class="mb-3">
        <label class="form-label">نوع البند</label>
        <select name="type" class="form-select" required>
            <option value="budget">موازنة</option>
            <option value="revenue">إيراد</option>
            <option value="expense">مصروف</option>
        </select>
    </div> --}}

    <div class="mb-3">
        <label class="form-label">الاسم</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">المبلغ</label>
        <input type="number" name="amount" step="0.01"  value="0" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">الرئيسي (اختياري)</label>
        <select name="parent_id" class="form-control">
            <option value="">— لا شيء —</option>
            @foreach ($allItems as $item)
                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->type }})</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">💾 حفظ</button>
</form>
