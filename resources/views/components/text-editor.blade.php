 @props(['name', 'height' => 'h-96', 'value'])

 <div>
     <style>
         @import url(https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.min.css);

         /**
         * tailwind.config.js
         * module.exports = {
         *   variants: {
         *     extend: {
         *       backgroundColor: ['active'],
         *     }
         *   },
         * }
         */
         .active\:bg-gray-50:active {
             --tw-bg-opacity: 1;
             background-color: rgba(249, 250, 251, var(--tw-bg-opacity));
         }
     </style>
     <div class="w-full max-w-6xl mx-auto rounded-xl p-5 text-black" x-data="editor()" x-init="content = @js($value ?? ''); init($refs.wysiwyg)">
         <div class="border border-gray-500 bg-white overflow-hidden rounded-md">
             <div class="w-full flex flex-wrap border-b border-gray-500 text-xl text-gray-600">
                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('bold')">
                     <i class="mdi mdi-format-bold"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('italic')">
                     <i class="mdi mdi-format-italic"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 mr-1 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('underline')">
                     <i class="mdi mdi-format-underline"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-l border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('formatBlock','P')">
                     <i class="mdi mdi-format-paragraph"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('formatBlock','H1')">
                     <i class="mdi mdi-format-header-1"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('formatBlock','H2')">
                     <i class="mdi mdi-format-header-2"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 mr-1 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('formatBlock','H3')">
                     <i class="mdi mdi-format-header-3"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-l border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('insertUnorderedList')">
                     <i class="mdi mdi-format-list-bulleted"></i>
                 </button>
                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 mr-1 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('insertOrderedList')">
                     <i class="mdi mdi-format-list-numbered"></i>
                 </button>

                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="fontSize('increase')">
                     <i class="mdi mdi-format-font-size-increase"></i>
                 </button>

                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="fontSize('decrease')">
                     <i class="mdi mdi-format-font-size-decrease"></i>
                 </button>


                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('justifyRight')">
                     <i class="mdi mdi-format-align-right"></i>
                 </button>

                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('justifyCenter')">
                     <i class="mdi mdi-format-align-center"></i>
                 </button>

                 <button type="button"
                     class="outline-none focus:outline-none border-r border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('justifyLeft')">
                     <i class="mdi mdi-format-align-left"></i>
                 </button>


                 <!-- Image Upload Button -->
                 <button type="button"
                     class="outline-none focus:outline-none border-l border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="uploadImage()">
                     <i class="mdi mdi-image"></i>
                 </button>

                 <!-- Color Picker -->
                 <button type="button"
                     class="outline-none focus:outline-none border-l border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="openColorPicker()">
                     <i class="mdi mdi-palette"></i>
                 </button>

                 <!-- YouTube Video Embed -->
                 <button type="button"
                     class="outline-none focus:outline-none border-l border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="embedYouTube()">
                     <i class="mdi mdi-youtube"></i>
                 </button>

                 <!-- add link -->
                 <button type="button"
                     class="outline-none focus:outline-none border-l border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="addLink()">
                     <i class="mdi mdi-link"></i>
                 </button>

                 <button type="button"
                     class="outline-none focus:outline-none border-l border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('removeFormat')">
                     <i class="mdi mdi-format-clear"></i>
                 </button>

                 <button title="تراجع" type="button"
                     class="outline-none focus:outline-none border-l border-gray-200 w-10 h-10 hover:text-secondary-500 active:bg-gray-50"
                     @click="format('undo')">
                     <i class="fas fa-undo"></i>
                 </button>
             </div>
             <div class="w-full" class="p-5" x-on:input="content = $event.target.innerHTML">
                 <iframe x-ref="wysiwyg" class="w-full {{$height}} overflow-y-auto"
                     data-wysiwyg="{{ $name }}"
                     src="about:blank"></iframe>
                 <textarea name="{{ $name }}" class="hidden" x-model="content">
                 </textarea>
             </div>

             <!-- Hidden file input for image upload -->
             <input type="file" x-ref="imageInput" class="hidden" accept="image/*" @change="handleImageUpload($event)">
             
             <!-- Color Picker Modal -->
             <div x-show="showColorPicker" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                 <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                     <h3 class="text-lg font-semibold mb-4">Choose Color</h3>
                     <div class="grid grid-cols-8 gap-2 mb-4">
                         <template x-for="color in colorPalette" :key="color">
                             <button type="button" 
                                 class="w-8 h-8 rounded border-2 border-gray-300 hover:border-gray-400"
                                 :style="'background-color: ' + color"
                                 @click="applyColor(color)">
                             </button>
                         </template>
                     </div>
                     <div class="flex space-x-2">
                         <input type="color" x-ref="customColor" class="w-12 h-8 border border-gray-300 rounded" @change="applyColor($event.target.value)">
                         <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600" @click="showColorPicker = false">Cancel</button>
                     </div>
                 </div>
             </div>

         </div>
     </div>

 </div>

 @push('scripts')
     <script>
function editor() {
    return {
        wysiwyg: null,
        content: '',
        showColorPicker: false,
        colorPalette: [
            '#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF',
            '#FFA500', '#800080', '#008000', '#FFC0CB', '#A52A2A', '#808080', '#000080', '#800000',
            '#FFD700', '#32CD32', '#FF1493', '#00CED1', '#FF4500', '#8A2BE2', '#228B22', '#DC143C'
        ],
        init: function(el) {
            this.wysiwyg = el;
            this.isTyping = false;
            this.typingTimeout = null;
     
                const head = this.wysiwyg.contentDocument.head.innerHTML += `<style>
                    *, ::after, ::before {box-sizing: border-box;}
                    :root {tab-size: 4;}
                    html {line-height: 1.15; direction: rtl;}
                    body {margin: 0px; padding: 1rem 0.5rem; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";}
                                    img {user-select: none; position: relative; display: block; margin: 10px 0; min-height: 50px;}
                .youtube-embed {user-select: none; display: block; margin: 10px 0; position: relative; width: 280px; height: 157px; min-height: 50px;}
                    .image-wrapper {position: relative; display: inline-block; margin: 10px 0; width: fit-content; height: fit-content; z-index: 1; float: none; clear: both;}
    
                    .delete-btn {position: absolute; top: 5px; right: 5px; background: rgba(239, 68, 68, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: block !important; font-size: 12px; z-index: 1001; pointer-events: none !important;}
                    .delete-btn:hover {background: rgba(239, 68, 68, 1);}
                    .resize-handle {position: absolute; bottom: 5px; left: 5px; background: rgba(59, 130, 246, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: se-resize; display: block !important; font-size: 12px; z-index: 1001; pointer-events: none !important;}
                    .resize-handle:hover {background: rgba(59, 130, 246, 1);}
                </style>`;

                // Make editable
                this.wysiwyg.contentDocument.designMode = "on";
                
                // Add typing detection to prevent cursor jumping
                this.wysiwyg.contentDocument.addEventListener('input', () => {
                    this.isTyping = true;
                    if (this.typingTimeout) {
                        clearTimeout(this.typingTimeout);
                    }
                    this.typingTimeout = setTimeout(() => {
                        this.isTyping = false;
                    }, 100);
                });

                // Ensure the iframe body is ready before setting content
                setTimeout(() => {
                    if (this.content && this.content.length > 0) {
                        this.wysiwyg.contentDocument.body.innerHTML = this.content;
                    }
                    this.setupImageControls();
                    this.setupVideoControls();
                    
                    // Ensure elements with custom dimensions maintain their size
                    this.preserveCustomDimensions();
                }, 50);

                // Watch for changes to content and update iframe body
                this.$watch('content', value => {
                    if (this.wysiwyg && this.wysiwyg.contentDocument.body.innerHTML !== value) {
                        // Don't update iframe body during typing to prevent cursor jumping
                        if (!this.isTyping) {
                            this.wysiwyg.contentDocument.body.innerHTML = value;
                            
                                                                            // Setup controls with delay to ensure DOM is ready
                            setTimeout(() => {
                                this.setupImageControls();
                                this.setupVideoControls();
                            }, 50);
                        } else {
                            // If typing, still setup controls but don't update iframe body
                            setTimeout(() => {
                                this.setupImageControls();
                                this.setupVideoControls();
                            }, 50);
                        }
                    }
                });

                const observer = new MutationObserver((mutations) => {
                    // Check if any mutation is related to text input (characterData changes)
                    const hasTextChanges = mutations.some(mutation => 
                        mutation.type === 'characterData' || 
                        (mutation.type === 'childList' && mutation.target.nodeType === Node.TEXT_NODE)
                    );
                    
                    // If there are text changes, don't interfere with typing
                    if (hasTextChanges) {
                        return;
                    }
                    
                    // Only update content if there are actual changes (not just delete button additions/removals)
                    const currentContent = this.wysiwyg.contentDocument.body.innerHTML;
                    
                    // Remove all delete buttons and resize handles from the content
                    let cleanContent = currentContent;
                    
                    // Remove delete buttons (both button and div elements)
                    cleanContent = cleanContent.replace(/<button[^>]*class="[^"]*delete-btn[^"]*"[^>]*>.*?<\/button>/g, '');
                    cleanContent = cleanContent.replace(/<button[^>]*class="[^"]*delete-btn[^"]*"[^>]*\/>/g, '');
                    cleanContent = cleanContent.replace(/<div[^>]*class="[^"]*delete-btn[^"]*"[^>]*>.*?<\/div>/g, '');
                    cleanContent = cleanContent.replace(/<div[^>]*class="[^"]*delete-btn[^"]*"[^>]*\/>/g, '');
                    
                    // Remove resize handles (both button and div elements)
                    cleanContent = cleanContent.replace(/<button[^>]*class="[^"]*resize-handle[^"]*"[^>]*>.*?<\/button>/g, '');
                    cleanContent = cleanContent.replace(/<button[^>]*class="[^"]*resize-handle[^"]*"[^>]*\/>/g, '');
                    cleanContent = cleanContent.replace(/<div[^>]*class="[^"]*resize-handle[^"]*"[^>]*>.*?<\/div>/g, '');
                    cleanContent = cleanContent.replace(/<div[^>]*class="[^"]*resize-handle[^"]*"[^>]*\/>/g, '');
                    
                    // Clean up any empty style attributes that might be left
                    cleanContent = cleanContent.replace(/ style=""/g, '');
                    cleanContent = cleanContent.replace(/ style=";"/g, '');
                    cleanContent = cleanContent.replace(/ style="; "/g, '');
                    
                    // Only update if content actually changed and it's not just control buttons being added/removed
                    if (this.content !== cleanContent && cleanContent.length > 0) {
                        this.content = cleanContent; // Update content variable

                        if (this.content.length > 0) {
                            window.dispatchEvent(new CustomEvent('content-updated', {
                                detail: {
                                    content: this.content
                                }
                            }));
                        }
                    }
                });

                            // Start observing the body for changes
                observer.observe(this.wysiwyg.contentDocument.body, { childList: true, subtree: true, characterData: true });

                // Clean content before form submission
                const form = this.wysiwyg.closest('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        this.cleanContentForSaving();
                    });
                }

        },
        setupImageControls: function() {
            const images = this.wysiwyg.contentDocument.querySelectorAll('img');
            console.log('Found', images.length, 'images to setup');
            
            // Skip if already processing
            if (this.setupInProgress) {
                console.log('Setup already in progress, skipping');
                return;
            }
            
            this.setupInProgress = true;
            
            // Remove any existing duplicate buttons first
            const existingDeleteBtns = this.wysiwyg.contentDocument.querySelectorAll('.delete-btn');
            const existingResizeHandles = this.wysiwyg.contentDocument.querySelectorAll('.resize-handle');
            
            // If there are more buttons than images, remove duplicates
            if (existingDeleteBtns.length > images.length) {
                console.log('Removing duplicate buttons');
                existingDeleteBtns.forEach((btn, index) => {
                    if (index >= images.length) {
                        btn.remove();
                    }
                });
            }
            
            if (existingResizeHandles.length > images.length) {
                console.log('Removing duplicate resize handles');
                existingResizeHandles.forEach((handle, index) => {
                    if (index >= images.length) {
                        handle.remove();
                    }
                });
            }
            
            images.forEach(img => {
                // Ensure image has proper positioning context from the start
                img.style.position = 'relative';
                img.style.display = 'block';
                img.style.minHeight = '50px';
                img.style.minWidth = '50px';
                img.style.overflow = 'visible';
                img.style.zIndex = '1';
                console.log('Image positioning set:', img.style.position, img.style.display);
                console.log('Image dimensions:', img.offsetWidth, 'x', img.offsetHeight);
                console.log('Image natural dimensions:', img.naturalWidth, 'x', img.naturalHeight);
                console.log('Image getBoundingClientRect:', img.getBoundingClientRect());
                console.log('Image parent:', img.parentElement);
                console.log('Image parent dimensions:', img.parentElement?.offsetWidth, 'x', img.parentElement?.offsetHeight);
                console.log('Image can have children:', img.children.length);
                console.log('Image tagName:', img.tagName);
                console.log('Image is in DOM:', this.wysiwyg.contentDocument.contains(img));
                
                // Check if image already has a wrapper
                let wrapper = img.parentElement;
                let needsWrapper = false;
                
                if (!wrapper || !wrapper.classList.contains('image-wrapper')) {
                    needsWrapper = true;
                    console.log('Image needs wrapper - creating new wrapper');
                } else {
                    console.log('Image already has wrapper:', wrapper);
                    // Ensure existing wrapper has proper positioning
                    if (wrapper.style.position !== 'relative') {
                        wrapper.style.position = 'relative';
                        wrapper.style.display = 'inline-block';
                        wrapper.style.margin = '10px 0';
                        wrapper.style.width = 'fit-content';
                        wrapper.style.height = 'fit-content';
                        wrapper.style.zIndex = '1';
                        wrapper.style.float = 'none';
                        wrapper.style.clear = 'both';
                    }
                }
                
                // Create wrapper if needed
                if (needsWrapper) {
                    wrapper = this.wysiwyg.contentDocument.createElement('div');
                    wrapper.className = 'image-wrapper';
                    wrapper.style.position = 'relative';
                    wrapper.style.display = 'inline-block';
                    wrapper.style.margin = '10px 0';
                    wrapper.style.width = 'fit-content';
                    wrapper.style.height = 'fit-content';
                    wrapper.style.zIndex = '1';
                    wrapper.style.float = 'none';
                    wrapper.style.clear = 'both';
                    
                    // Move the image into the wrapper
                    img.parentNode.insertBefore(wrapper, img);
                    wrapper.appendChild(img);
                    console.log('Wrapper created with position:', wrapper.style.position);
                    console.log('Wrapper dimensions:', wrapper.offsetWidth, 'x', wrapper.offsetHeight);
                }
                
                // Add delete button if it doesn't exist
                if (!wrapper.querySelector('.delete-btn')) {
                    console.log('Creating delete button');
                    
                    // Create delete button
                    const deleteBtn = this.wysiwyg.contentDocument.createElement('div');
                    deleteBtn.className = 'delete-btn';
                    deleteBtn.innerHTML = '×';
                    deleteBtn.title = 'Delete image';
                    deleteBtn.style.cssText = `
                        position: absolute;
                        top: 5px;
                        right: 5px;
                        z-index: 9999;
                        display: block !important;
                        background: rgba(239, 68, 68, 0.9);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 24px;
                        height: 24px;
                        min-width: 24px;
                        min-height: 24px;
                        cursor: pointer;
                        font-size: 12px;
                        box-sizing: border-box;
                        padding: 0;
                        margin: 0;
                        line-height: 24px;
                        text-align: center;
                        font-weight: bold;
                        user-select: none;
                        opacity: 1 !important;
                        visibility: visible !important;
                        pointer-events: none !important;
                        touch-action: none;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        tabindex: -1;
                    `;
                    deleteBtn.setAttribute('tabindex', '-1');
                    deleteBtn.setAttribute('contenteditable', 'false');
                    deleteBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        wrapper.remove();
                    });
                    
                    // Prevent any text selection or focus
                    deleteBtn.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                    
                    deleteBtn.addEventListener('focus', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        deleteBtn.blur();
                    });
                    
                    // Append button to wrapper
                    wrapper.appendChild(deleteBtn);
                    console.log('Delete button created and appended to wrapper');
                    
                    // Ensure button is properly positioned
                    deleteBtn.style.position = 'absolute';
                    deleteBtn.style.top = '5px';
                    deleteBtn.style.right = '5px';
                    deleteBtn.style.zIndex = '9999';
                }
                
                // Add resize handle if it doesn't exist
                if (!wrapper.querySelector('.resize-handle')) {
                    console.log('Creating resize handle');
                    
                    const resizeHandle = this.wysiwyg.contentDocument.createElement('div');
                    resizeHandle.className = 'resize-handle';
                    resizeHandle.innerHTML = '⤡';
                    resizeHandle.title = 'Resize image';
                    resizeHandle.style.cssText = `
                        position: absolute;
                        bottom: 5px;
                        left: 5px;
                        z-index: 9999;
                        display: block !important;
                        background: rgba(59, 130, 246, 0.9);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 24px;
                        height: 24px;
                        min-width: 24px;
                        min-height: 24px;
                        cursor: se-resize;
                        font-size: 12px;
                        box-sizing: border-box;
                        padding: 0;
                        margin: 0;
                        line-height: 24px;
                        text-align: center;
                        font-weight: bold;
                        user-select: none;
                        opacity: 1 !important;
                        visibility: visible !important;
                        pointer-events: none !important;
                        touch-action: none;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        tabindex: -1;
                    `;
                    resizeHandle.setAttribute('tabindex', '-1');
                    resizeHandle.setAttribute('contenteditable', 'false');
                    resizeHandle.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.startResize(e, img);
                    });
                    
                    // Append resize handle to wrapper
                    wrapper.appendChild(resizeHandle);
                    console.log('Resize handle created and appended to wrapper');
                    
                    // Ensure resize handle is properly positioned
                    resizeHandle.style.position = 'absolute';
                    resizeHandle.style.bottom = '5px';
                    resizeHandle.style.left = '5px';
                    resizeHandle.style.zIndex = '9999';
                }
                
                // Add hover functionality to enable pointer events (for both new and existing wrappers)
                const deleteBtn = wrapper.querySelector('.delete-btn');
                const resizeHandle = wrapper.querySelector('.resize-handle');
                
                if (deleteBtn && resizeHandle) {
                    // Ensure buttons are always properly positioned
                    deleteBtn.style.position = 'absolute';
                    deleteBtn.style.top = '5px';
                    deleteBtn.style.right = '5px';
                    deleteBtn.style.zIndex = '9999';
                    deleteBtn.style.display = 'block';
                    deleteBtn.style.opacity = '1';
                    deleteBtn.style.visibility = 'visible';
                    
                    resizeHandle.style.position = 'absolute';
                    resizeHandle.style.bottom = '5px';
                    resizeHandle.style.left = '5px';
                    resizeHandle.style.zIndex = '9999';
                    resizeHandle.style.display = 'block';
                    resizeHandle.style.opacity = '1';
                    resizeHandle.style.visibility = 'visible';
                    
                    wrapper.addEventListener('mouseenter', () => {
                        deleteBtn.style.pointerEvents = 'auto';
                        deleteBtn.style.setProperty('pointer-events', 'auto', 'important');
                        resizeHandle.style.pointerEvents = 'auto';
                        resizeHandle.style.setProperty('pointer-events', 'auto', 'important');
                    });
                    
                    wrapper.addEventListener('mouseleave', () => {
                        deleteBtn.style.pointerEvents = 'none';
                        deleteBtn.style.setProperty('pointer-events', 'none', 'important');
                        resizeHandle.style.pointerEvents = 'none';
                        resizeHandle.style.setProperty('pointer-events', 'none', 'important');
                    });
                }
                
                // Log button creation for debugging
                setTimeout(() => {
                    const wrapper = img.parentElement;
                    const deleteBtn = wrapper.querySelector('.delete-btn');
                    const resizeHandle = wrapper.querySelector('.resize-handle');
                    if (deleteBtn) {
                        console.log('Delete button final state:', deleteBtn.offsetWidth, 'x', deleteBtn.offsetHeight);
                        console.log('Delete button display:', window.getComputedStyle(deleteBtn).display);
                    }
                    if (resizeHandle) {
                        console.log('Resize handle final state:', resizeHandle.offsetWidth, 'x', resizeHandle.offsetHeight);
                        console.log('Resize handle display:', window.getComputedStyle(resizeHandle).display);
                    }
                }, 100);
                

                

                
                // Mark image as having controls setup
                if (!img.hasAttribute('data-controls-setup')) {
                    img.setAttribute('data-controls-setup', 'true');
                    console.log('Controls setup completed for image');
                }
            });
            
            // Reset setup flag
            this.setupInProgress = false;
        },


        setupVideoControls: function() {
            const videos = this.wysiwyg.contentDocument.querySelectorAll('.youtube-embed');
            videos.forEach(video => {
                // Add delete button if it doesn't exist
                if (!video.querySelector('.delete-btn')) {
                    const deleteBtn = this.wysiwyg.contentDocument.createElement('button');
                    deleteBtn.className = 'delete-btn';
                    deleteBtn.innerHTML = '×';
                    deleteBtn.title = 'Delete video';
                    deleteBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        video.remove();
                    });
                    video.appendChild(deleteBtn);
                }
                
                // Add resize handle if it doesn't exist
                if (!video.querySelector('.resize-handle')) {
                    // Ensure video has proper positioning context
                    video.style.position = 'relative';
                    video.style.display = 'block';
                    
                    const resizeHandle = this.wysiwyg.contentDocument.createElement('button');
                    resizeHandle.className = 'resize-handle';
                    resizeHandle.innerHTML = '⤡';
                    resizeHandle.title = 'Resize video';
                    resizeHandle.style.position = 'absolute';
                    resizeHandle.style.bottom = '5px';
                    resizeHandle.style.left = '5px';
                    resizeHandle.style.zIndex = '1001';
                    resizeHandle.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.startResize(e, video);
                    });
                    video.appendChild(resizeHandle);
                }
            });
        },
        startResize: function(e, element) {
            const startX = e.clientX;
            const startY = e.clientY;
            const startWidth = element.offsetWidth;
            const startHeight = element.offsetHeight;
            
            const handleMouseMove = (e) => {
                const deltaX = e.clientX - startX;
                const deltaY = e.clientY - startY;
                
                // Calculate new dimensions with intuitive resize direction
                // Drag left increases width, drag down increases height
                let newWidth = startWidth - deltaX;  // Negative deltaX (left) = positive width
                let newHeight = startHeight + deltaY; // Positive deltaY (down) = positive height
                
                // Apply new dimensions as inline styles (no size constraints)
                element.setAttribute('style', element.getAttribute('style') + '; width: ' + newWidth + 'px; height: ' + newHeight + 'px;');
                
                // For videos, also resize the iframe inside
                if (element.classList.contains('youtube-embed')) {
                    const iframe = element.querySelector('iframe');
                    if (iframe) {
                        iframe.setAttribute('style', iframe.getAttribute('style') + '; width: 100%; height: 100%;');
                    }
                }
                
                // Force content update to preserve the new dimensions
                this.content = this.wysiwyg.contentDocument.body.innerHTML;
            };
            
            const handleMouseUp = () => {
                this.wysiwyg.contentDocument.removeEventListener('mousemove', handleMouseMove);
                this.wysiwyg.contentDocument.removeEventListener('mouseup', handleMouseUp);
            };
            
            this.wysiwyg.contentDocument.addEventListener('mousemove', handleMouseMove);
            this.wysiwyg.contentDocument.addEventListener('mouseup', handleMouseUp);
        },
        preserveCustomDimensions: function() {
            // Ensure images and videos maintain their custom dimensions
            const images = this.wysiwyg.contentDocument.querySelectorAll('img');
            const videos = this.wysiwyg.contentDocument.querySelectorAll('.youtube-embed');
            
            [...images, ...videos].forEach(element => {
                // If element has inline width/height styles, ensure they're preserved
                if (element.getAttribute('style') && (element.getAttribute('style').includes('width') || element.getAttribute('style').includes('height'))) {
                    // Ensure positioning context is maintained
                    let currentStyle = element.getAttribute('style') || '';
                    if (!currentStyle.includes('position: relative')) {
                        element.setAttribute('style', currentStyle + '; position: relative; display: block;');
                    }
                    
                    // For videos, ensure iframe fills the container
                    if (element.classList.contains('youtube-embed')) {
                        const iframe = element.querySelector('iframe');
                        if (iframe) {
                            let iframeStyle = iframe.getAttribute('style') || '';
                            if (!iframeStyle.includes('width: 100%')) {
                                iframe.setAttribute('style', iframeStyle + '; width: 100%; height: 100%;');
                            }
                        }
                    }
                }
            });
        },
        cleanContentForSaving: function() {
            // Remove all control buttons before saving
            const deleteButtons = this.wysiwyg.contentDocument.querySelectorAll('.delete-btn');
            const resizeHandles = this.wysiwyg.contentDocument.querySelectorAll('.resize-handle');
            
            // Remove delete buttons
            deleteButtons.forEach(btn => btn.remove());
            
            // Remove resize handles
            resizeHandles.forEach(btn => btn.remove());
            
            // Update content without control buttons
            this.content = this.wysiwyg.contentDocument.body.innerHTML;
        },
        format: function(cmd, param) {
            this.wysiwyg.contentDocument.execCommand(cmd, false, param || null);
            this.wysiwyg.contentDocument.body.focus();
        },
        uploadImage: function() {
            this.$refs.imageInput.click();
        },
        handleImageUpload: function(event) {
            const file = event.target.files[0];
            if (file) {
                // Create FormData to upload the file
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Upload the file to the server
                fetch('/upload-image', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Insert the uploaded image URL
                        this.wysiwyg.contentDocument.execCommand('insertImage', false, data.url);
                        this.setupImageControls();
                        this.wysiwyg.contentDocument.body.focus();
                    } else {
                        alert('Image upload failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Image upload failed. Please try again.');
                });
            }
            // Reset the input
            event.target.value = '';
        },
        openColorPicker: function() {
            this.showColorPicker = true;
        },
        applyColor: function(color) {
            this.wysiwyg.contentDocument.execCommand('foreColor', false, color);
            this.wysiwyg.contentDocument.body.focus();
            this.showColorPicker = false;
        },
        embedYouTube: function() {
            const url = prompt('Enter YouTube video URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID)');
            if (url) {
                const videoId = this.extractYouTubeId(url);
                if (videoId) {
                    const embedHtml = `<div class="youtube-embed" contenteditable="false">
                        <iframe width="100%" height="100%" 
                            src="https://www.youtube.com/embed/${videoId}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>`;
                    
                    this.wysiwyg.contentDocument.execCommand('insertHTML', false, embedHtml);
                    this.setupVideoControls();
                    this.wysiwyg.contentDocument.body.focus();
                } else {
                    alert('Invalid YouTube URL. Please enter a valid YouTube video URL.');
                }
            }
            this.wysiwyg.contentDocument.body.focus();
        },
        extractYouTubeId: function(url) {
            const patterns = [
                /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/,
                /youtube\.com\/v\/([^&\n?#]+)/,
                /youtube\.com\/watch\?.*v=([^&\n?#]+)/
            ];
            
            for (let pattern of patterns) {
                const match = url.match(pattern);
                if (match) {
                    return match[1];
                }
            }
            return null;
        },
        addLink: function() {
            const url = prompt('Enter the link URL');
            if (url) {
                this.wysiwyg.contentDocument.execCommand('createLink', false, url);
            }
            this.wysiwyg.contentDocument.body.focus();
        },
        fontSize: function(cmd) {
            const sizeMapping = { 1: "1", 2: "2", 3: "3", 4: "4", 5: "5", 6: "6", 7: "7" };
            let currentSize = parseInt(this.wysiwyg.contentDocument.queryCommandValue('fontSize')) || 3;

            if (cmd === 'increase' && currentSize < 7) {
                currentSize += 1;
            } else if (cmd === 'decrease' && currentSize > 1) {
                currentSize -= 1;
            }

            this.wysiwyg.contentDocument.execCommand('fontSize', false, sizeMapping[currentSize]);
            this.wysiwyg.contentDocument.body.focus();
        }
    }
}

     </script>
 @endpush
 