// Prevenir el registro del componente web inmediatamente
(function() {
    if (!window.customElementsRegistry) {
        window.customElementsRegistry = new Map();
    }

    if (window.customElements) {
        const originalDefine = window.customElements.define;
        window.customElements.define = function(name, constructor, options) {
            if (window.customElementsRegistry.has(name)) {
                console.warn(`Component ${name} already registered, skipping registration`);
                return;
            }

            try {
                originalDefine.call(window.customElements, name, constructor, options);
                window.customElementsRegistry.set(name, true);
            } catch (e) {
                console.warn(`Error registering ${name}:`, e);
            }
        };
    }
})();

import './bootstrap';

// Importar TinyMCE desde CDN
const script = document.createElement('script');
script.src = 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js';
script.referrerpolicy = 'origin';

// Cargar nuestro script de inicialización personalizado después de que TinyMCE esté listo
script.onload = () => {
    const initScript = document.createElement('script');
    initScript.src = '/js/tinymce-init.js';
    document.head.appendChild(initScript);

    // Inicializar TinyMCE cuando nuestro script esté listo
    initScript.onload = () => {
        // Inicializar TinyMCE en todos los textareas con la clase 'tinymce'
        window.initTinyMCE('textarea.tinymce');
    };
};

document.head.appendChild(script);
