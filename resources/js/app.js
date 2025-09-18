// App JS entry loaded by Vite
// Alpine and Livewire are loaded via @livewireScripts in the layout
// We just need to initialize plugins after Alpine is loaded

document.addEventListener('alpine:init', () => {
    // Import and initialize toast component when Alpine is ready
    import('../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts').then(ToastComponent => {
        // Call the function with Alpine to register ToastComponent
        ToastComponent.default(window.Alpine);
        console.log('Toast component loaded successfully');
    }).catch(error => {
        console.error('Failed to load toast component:', error);
    });
});
