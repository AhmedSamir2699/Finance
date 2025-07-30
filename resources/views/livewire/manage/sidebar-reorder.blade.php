<div>
    @if($links->count() > 0)
        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div id="sortable-links" class="space-y-2">
                    @foreach($links as $link)
                        <div class="bg-white p-3 rounded border cursor-move hover:shadow-md transition-shadow" 
                             data-id="{{ $link->id }}" data-type="parent">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 gap-3">
                                    <i class="{{ $link->icon }} text-gray-500"></i>
                                    <span class="font-medium">{{ $link->title }}</span>
                                    @if($link->children->count() > 0)
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            {{ $link->children->count() }} {{ __('sidebar.children') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex gap-3 items-center space-x-2">
                                    <div class="text-sm text-gray-500">
                                        {{ __('sidebar.order') }}: {{ $link->order }}
                                    </div>
                                    @if($link->children->count() > 0)
                                        <button type="button" onclick="toggleChildren({{ $link->id }})" class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="fas fa-chevron-left" id="chevron-{{ $link->id }}"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            @if($link->children->count() > 0)
                                <div id="children-{{ $link->id }}" class="mt-3 ml-6" style="display: none;">
                                    <div id="sortable-children-{{ $link->id }}" class="space-y-1">
                                        @foreach($link->children as $child)
                                            <div class="bg-gray-50 p-2 rounded border cursor-move hover:bg-gray-100 transition-colors" 
                                                 data-id="{{ $child->id }}" data-parent="{{ $link->id }}" data-type="child">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2 gap-3">
                                                        <i class="{{ $child->icon }} text-gray-500 text-sm"></i>
                                                        <span class="text-sm font-medium">{{ $child->title }}</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ __('sidebar.order') }}: {{ $child->order }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-info-circle text-gray-400 text-4xl mb-4"></i>
            <p class="text-gray-500">{{ __('sidebar.no_links_found') }}</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize parent links sortable
    const parentSortable = new Sortable(document.getElementById('sortable-links'), {
        animation: 150,
        ghostClass: 'bg-blue-50',
        onEnd: function(evt) {
            updateOrderNumbers();
            saveOrderOnDrop();
        }
    });
    
    // Initialize children sortables for each parent
    @foreach($links as $link)
        @if($link->children->count() > 0)
            const childrenSortable{{ $link->id }} = new Sortable(document.getElementById('sortable-children-{{ $link->id }}'), {
                animation: 150,
                ghostClass: 'bg-blue-50',
                group: 'children',
                onEnd: function(evt) {
                    updateOrderNumbers();
                    saveOrderOnDrop();
                }
            });
        @endif
    @endforeach
    
    function updateOrderNumbers() {
        // Update parent order numbers
        const parentItems = document.querySelectorAll('#sortable-links > div[data-type="parent"]');
        parentItems.forEach((item, index) => {
            const orderSpan = item.querySelector('.text-sm.text-gray-500');
            if (orderSpan) {
                orderSpan.textContent = '{{ __('sidebar.order') }}: ' + (index + 1);
            }
        });
        
        // Update children order numbers
        const childGroups = document.querySelectorAll('[id^="sortable-children-"]');
        childGroups.forEach(group => {
            const childItems = group.querySelectorAll('[data-type="child"]');
            childItems.forEach((item, index) => {
                const orderSpan = item.querySelector('.text-xs.text-gray-500');
                if (orderSpan) {
                    orderSpan.textContent = '{{ __('sidebar.order') }}: ' + (index + 1);
                }
            });
        });
    }
    
    function saveOrderOnDrop() {
        const orderData = {
            parents: [],
            children: {}
        };
        
        // Collect parent order data
        const parentItems = document.querySelectorAll('#sortable-links > div[data-type="parent"]');
        parentItems.forEach((item, index) => {
            orderData.parents.push({
                id: parseInt(item.dataset.id),
                order: index + 1
            });
        });
        
        // Collect children order data
        const childGroups = document.querySelectorAll('[id^="sortable-children-"]');
        childGroups.forEach(group => {
            const parentId = group.id.replace('sortable-children-', '');
            const childItems = group.querySelectorAll('[data-type="child"]');
            orderData.children[parentId] = [];
            
            childItems.forEach((item, index) => {
                orderData.children[parentId].push({
                    id: parseInt(item.dataset.id),
                    order: index + 1
                });
            });
        });
        
        // Send to server using fetch
        fetch('{{ route("manage.sidebar-links.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('{{ __("sidebar.messages.reordered") }}');
                }
            }
        })
        .catch(error => {
            console.error('Error saving order:', error);
        });
    }
    
    // Add click outside modal to reload page
    document.addEventListener('click', function(event) {
        // Only handle clicks when modal is open
        const modal = document.querySelector('[x-data*="reorderModal"]');
        if (!modal) return;
        
        // Check if modal is visible (has Alpine.js data)
        const modalData = Alpine.$data(modal);
        if (!modalData || !modalData.reorderModal) return;
        
        // Check if click is on the backdrop overlay using specific ID
        const backdrop = document.getElementById('modal-backdrop-reorderModal');
        if (backdrop && event.target === backdrop) {
            // Clicked on backdrop overlay, reload page
            window.location.reload();
        }
    });
    
    // Also handle escape key to reload
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.querySelector('[x-data*="reorderModal"]');
            if (modal) {
                const modalData = Alpine.$data(modal);
                if (modalData && modalData.reorderModal) {
                    window.location.reload();
                }
            }
        }
    });
});

function toggleChildren(parentId) {
    const childrenDiv = document.getElementById(`children-${parentId}`);
    const chevron = document.getElementById(`chevron-${parentId}`);
    
    if (childrenDiv.style.display === 'none') {
        childrenDiv.style.display = 'block';
        chevron.classList.remove('fa-chevron-left');
        chevron.classList.add('fa-chevron-down');
    } else {
        childrenDiv.style.display = 'none';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-left');
    }
}
</script> 