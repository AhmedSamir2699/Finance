<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div x-data="imageEditor()" x-init="mounted()">
        <style>
            .variable-element {
                position: absolute;
                resize: both;
                overflow: hidden;
                word-wrap: break-word;
                min-width: 50px;
                min-height: 30px;
            }

            .resize-button {
                position: absolute;
                background-color: blue;
                color: white;
                width: 16px;
                height: 16px;
                cursor: pointer;
                text-align: center;
                line-height: 16px;
                font-size: 12px;
                font-weight: bold;
            }

            /* Button positioning */
            .resize-left {
                left: -8px;
                top: 50%;
                transform: translateY(-50%);
            }

            .resize-right {
                right: -8px;
                top: 50%;
                transform: translateY(-50%);
            }

            .resize-up {
                top: -8px;
                left: 50%;
                transform: translateX(-50%);
            }

            .resize-down {
                bottom: -8px;
                left: 50%;
                transform: translateX(-50%);
            }
        </style>

        <button
            style="width:20%; background-color: #2563eb; color: #fff; border: 1px solid #2563eb; display: inline-block; font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle; user-select: none; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; transition: color 0.15s; cursor: pointer; margin-top: 10px;"
            wire:click="mountAction('update', { variables: variables })">
            حفظ
        </button>

        <div class="relative" x-ref="imageContainer">
            <!-- Card Image -->
            <img src="{{ asset('storage/' . $record->background) }}" alt="Card Image" class="w-full h-auto" x-ref="image" />

            <template x-for="(variable, index) in variables" :key="index">
                <div class="absolute bg-blue-200 rounded cursor-move border border-red-500" {{-- style="display:flex; color: #2563eb; min-width:100px; text-align:center; font-weight: bold; border: 1px solid black; background:#fff" --}}
                    style="display: flex; justify-content: center; align-items: center; color: #2563eb; text-align: center; font-weight: bold; border: 1px solid black; background: rgba(255,255,255,.6); min-width: 100px; min-height: 50px; background-opacity: 0.5;"
                    :style="{
                        top: (variable.y * containerHeight) + 'px',
                        left: (variable.x * containerWidth) + 'px',
                        width: (variable.width * containerWidth) + 'px',
                        height: (variable.height * containerHeight) + 'px'
                    }"
                    @mousedown.prevent="startDrag($event, index)" @mouseup="stopDrag()">
                    <span x-text="variable.name" class="text-blue-600 font-bold"></span>

                    <!-- Resize Buttons -->
                    <div @click="resizeElement(index, 'left')" class="resize-button resize-left">-</div>
                    <div @click="resizeElement(index, 'right')" class="resize-button resize-right">+</div>
                    <div @click="resizeElement(index, 'up')" class="resize-button resize-up">-</div>
                    <div @click="resizeElement(index, 'down')" class="resize-button resize-down">+</div>
                </div>
            </template>

        </div>
    </div>


        @push('scripts')
            <script>
                function imageEditor() {
                    return {
                        variables: [
                            @foreach ($record->fields as $field)

                                {
                                    name: '{{ $field['name'] }}',
                                    x: {{ $field['x'] ?? 0 }},
                                    y: {{ $field['y'] ?? 0 }},
                                    width: {{ $field['width'] ?? 0.2 }},
                                    height: {{ $field['height'] ?? 0.05 }},
                                    type: '{{ $field['type'] ?? 'text' }}',
                                    size: '{{ $field['size'] ?? '25' }}',
                                    color: '{{ $field['color'] ?? 'rgb(0,0,0)' }}',
                                    font: '{{ $field['font'] ?? '1' }}',
                                }
                                @php
                                    if (!$loop->last) {
                                        echo ',';
                                    }
                                @endphp
                            @endforeach
                        ],
                        dragging: null,
                        startX: 0,
                        startY: 0,
                        offsetX: 0,
                        offsetY: 0,
                        elementWidth: 0,
                        elementHeight: 0,
                        containerPaddingLeft: 0,
                        containerPaddingTop: 0,

                        // Method to calculate the container's width, height, and padding relative to the screen
                        updateContainerDimensions() {
                            const imageContainer = this.$refs.imageContainer.getBoundingClientRect();
                            this.containerWidth = imageContainer.width;
                            this.containerHeight = imageContainer.height;

                            // Get the padding of the container to adjust the position calculations
                            const computedStyles = getComputedStyle(this.$refs.imageContainer);
                            this.containerPaddingLeft = parseFloat(computedStyles.paddingLeft);
                            this.containerPaddingTop = parseFloat(computedStyles.paddingTop);
                        },

                        // This will be called when the page loads or after any resize
                        initialize() {
                            this.updateContainerDimensions();
                            window.addEventListener('resize', this.updateContainerDimensions.bind(
                                this)); // Recalculate on window resize

                            window.onload = () => {
                                this.updateContainerDimensions();
                            };
                        },

                        // Start dragging and calculate relative position, accounting for padding and margin
                        startDrag(event, index) {
                            // Get the container and the image dimensions
                            const imageContainer = this.$refs.imageContainer.getBoundingClientRect();
                            const image = this.$refs.image;

                            // Get the dragged element and its dimensions
                            const element = this.$refs.imageContainer.children[index];
                            this.elementWidth = element.offsetWidth;
                            this.elementHeight = element.offsetHeight;

                            // Calculate the element's center point relative to the container
                            const centerX = this.variables[index].x * image.offsetWidth;
                            const centerY = this.variables[index].y * image.offsetHeight;

                            // Calculate the mouse's offset from the element's center
                            this.offsetX = event.clientX - imageContainer.left - this.containerPaddingLeft - centerX;
                            this.offsetY = event.clientY - imageContainer.top - this.containerPaddingTop - centerY;

                            // Start dragging
                            this.dragging = index;

                            document.addEventListener('mousemove', this.drag.bind(this));
                            document.addEventListener('mouseup', this.stopDrag.bind(this));
                        },

                        // Track mouse movement and update position in pixels
                        drag(event) {
                            if (this.dragging === null) return;

                            // Calculate the new mouse position relative to the image container
                            const imageContainer = this.$refs.imageContainer.getBoundingClientRect();

                            const deltaX = event.clientX - imageContainer.left - this.containerPaddingLeft - this.offsetX;
                            const deltaY = event.clientY - imageContainer.top - this.containerPaddingTop - this.offsetY;

                            // Update the variable position based on the new mouse position
                            this.variables[this.dragging].x = deltaX / this.$refs.image.offsetWidth;
                            this.variables[this.dragging].y = deltaY / this.$refs.image.offsetHeight;
                        },

                        stopDrag() {
                            document.removeEventListener('mousemove', this.drag.bind(this));
                            document.removeEventListener('mouseup', this.stopDrag.bind(this));
                            this.dragging = null;
                        },

                        resizeElement(index, direction) {
                            const step = 0.03; // Step size for resizing (as a percentage)
                            if (direction === 'left') {
                                this.variables[index].width = Math.max(this.variables[index].width - step,
                                    0.05); // Resize left (minimum width 5%)
                            } else if (direction === 'right') {
                                this.variables[index].width += step; // Resize right
                            } else if (direction === 'up') {
                                this.variables[index].height = Math.max(this.variables[index].height - step,
                                    0.05); // Resize up (minimum height 5%)
                            } else if (direction === 'down') {
                                this.variables[index].height += step; // Resize down
                            }
                        },
                        // Ensure the container is initialized when the page is loaded
                        mounted() {
                            this.initialize();
                        }
                    }
                }
            </script>
        @endpush
    
    </x-app-layout>
