(function (global) {
    'use strict';

    // ✅ Colores predefinidos
    const COLORS = [
        // Default (sin color)
        { label: 'Default', fill: undefined, stroke: undefined },

        // Colores básicos
        { label: 'Red', fill: '#FFCDD2', stroke: '#B71C1C' },
        { label: 'Pink', fill: '#F8BBD0', stroke: '#880E4F' },
        { label: 'Purple', fill: '#E1BEE7', stroke: '#4A148C' },
        { label: 'Deep Purple', fill: '#D1C4E9', stroke: '#311B92' },
        { label: 'Indigo', fill: '#C5CAE9', stroke: '#1A237E' },
        { label: 'Blue', fill: '#BBDEFB', stroke: '#0D47A1' },
        { label: 'Light Blue', fill: '#B3E5FC', stroke: '#01579B' },
        { label: 'Cyan', fill: '#B2EBF2', stroke: '#006064' },
        { label: 'Teal', fill: '#B2DFDB', stroke: '#004D40' },
        { label: 'Green', fill: '#C8E6C9', stroke: '#1B5E20' },
        { label: 'Light Green', fill: '#DCEDC8', stroke: '#33691E' },
        { label: 'Lime', fill: '#F0F4C3', stroke: '#827717' },
        { label: 'Yellow', fill: '#FFF9C4', stroke: '#F57F17' },
        { label: 'Amber', fill: '#FFE0B2', stroke: '#FF6F00' },
        { label: 'Orange', fill: '#FFE0B2', stroke: '#E65100' },
        { label: 'Deep Orange', fill: '#FFCCBC', stroke: '#BF360C' },
        { label: 'Brown', fill: '#D7CCC8', stroke: '#3E2723' },
        { label: 'Grey', fill: '#ECEFF1', stroke: '#263238' },
        { label: 'Blue Grey', fill: '#CFD8DC', stroke: '#263238' },

        // Tonos brillantes (Flat UI)
        { label: 'Turquoise', fill: '#B2EBF2', stroke: '#00838F' },
        { label: 'Emerald', fill: '#C8E6C9', stroke: '#1B5E20' },
        { label: 'Peter River', fill: '#BBDEFB', stroke: '#0D47A1' },
        { label: 'Amethyst', fill: '#E1BEE7', stroke: '#4A148C' },
        { label: 'Wet Asphalt', fill: '#CFD8DC', stroke: '#263238' },
        { label: 'Green Sea', fill: '#B2DFDB', stroke: '#004D40' },
        { label: 'Nephritis', fill: '#C8E6C9', stroke: '#1B5E20' },
        { label: 'Belize Hole', fill: '#BBDEFB', stroke: '#0D47A1' },
        { label: 'Wisteria', fill: '#E1BEE7', stroke: '#4A148C' },
        { label: 'Midnight Blue', fill: '#C5CAE9', stroke: '#1A237E' },

        // Estados / Categorías
        { label: 'Success', fill: '#D4EDDA', stroke: '#155724' },
        { label: 'Info', fill: '#D1ECF1', stroke: '#0C5460' },
        { label: 'Warning', fill: '#FFF3CD', stroke: '#856404' },
        { label: 'Danger', fill: '#F8D7DA', stroke: '#721C24' },
        { label: 'Primary', fill: '#D1E7DD', stroke: '#084298' },
        { label: 'Secondary', fill: '#E2E3E5', stroke: '#41464B' },
        { label: 'Light', fill: '#FEFEFE', stroke: '#6C757D' },
        { label: 'Dark', fill: '#D3D3D4', stroke: '#141619' },

        // Empresariales / Corporativos
        { label: 'Corporate Blue', fill: '#E3F2FD', stroke: '#0D47A1' },
        { label: 'Finance Green', fill: '#E8F5E8', stroke: '#2E7D32' },
        { label: 'Health Red', fill: '#FFEBEE', stroke: '#C62828' },
        { label: 'Tech Purple', fill: '#F3E5F5', stroke: '#4A148C' },
        { label: 'Education Yellow', fill: '#FFFDE7', stroke: '#F57F17' },
        { label: 'Logistics Orange', fill: '#FFF3E0', stroke: '#E65100' },
        { label: 'Energy Yellow', fill: '#FFFDE7', stroke: '#F9A825' },
        { label: 'Retail Pink', fill: '#FCE4EC', stroke: '#880E4F' },

        // Tonos neutros y modernos
        { label: 'White Smoke', fill: '#F5F5F5', stroke: '#666666' },
        { label: 'Silver', fill: '#EEEEEE', stroke: '#555555' },
        { label: 'Charcoal', fill: '#36454F', stroke: '#111111' },
        { label: 'Slate', fill: '#708090', stroke: '#2F4F4F' },
        { label: 'Navy', fill: '#001F3F', stroke: '#001F3F' },
        { label: 'Olive', fill: '#3D9970', stroke: '#3D9970' },
        { label: 'Maroon', fill: '#85144B', stroke: '#85144B' },
        { label: 'Aqua', fill: '#7FDBFF', stroke: '#0074D9' },
        { label: 'Fuchsia', fill: '#F012BE', stroke: '#F012BE' },
        { label: 'Lavender', fill: '#E6E6FA', stroke: '#5B3A70' },
        { label: 'Coral', fill: '#FF7F50', stroke: '#FF4500' },
        { label: 'Salmon', fill: '#FA8072', stroke: '#E9967A' },
        { label: 'Khaki', fill: '#F0E68C', stroke: '#BDB76B' },
        { label: 'Plum', fill: '#DDA0DD', stroke: '#8E4585' },
        { label: 'Crimson', fill: '#DC143C', stroke: '#DC143C' },
        { label: 'Gold', fill: '#FFD700', stroke: '#B8860B' },
        { label: 'Sky Blue', fill: '#87CEEB', stroke: '#4682B4' },
        { label: 'Tomato', fill: '#FF6347', stroke: '#C04000' },
        { label: 'Chocolate', fill: '#D2691E', stroke: '#6B3E26' },
        { label: 'Sandy Brown', fill: '#F4A460', stroke: '#A0522D' }
    ];

    // ✅ Icono SVG para el botón
    const colorImageSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor">
  <path d="m12.5 5.5.3-.4 3.6-3.6c.5-.5 1.3-.5 1.7 0l1 1c.5.4.5 1.2 0 1.7l-3.6 3.6-.4.2v.2c0 1.4.6 2 1 2.7v.6l-1.7 1.6c-.2.2-.4.2-.6 0L7.3 6.6a.4.4 0 0 1 0-.6l.3-.3.5-.5.8-.8c.2-.2.4-.1.6 0 .9.5 1.5 1.1 3 1.1zm-9.9 6 4.2-4.2 6.3 6.3-4.2 4.2c-.3.3-.9.3-1.2 0l-.8-.8-.9-.8-2.3-2.9" />
</svg>`;

    // ✅ ColorPopupProvider
    function ColorPopupProvider(eventBus, popupMenu, modeling, translate) {
        this._popupMenu = popupMenu;
        this._modeling = modeling;
        this._translate = translate || function(str) { return str; };
        this._colors = COLORS;
        this._defaultFillColor = 'white';
        this._defaultStrokeColor = 'rgb(34, 36, 42)';

        popupMenu.registerProvider('color-picker', this);
    }

    ColorPopupProvider.prototype.getEntries = function(element) {
        const self = this;

        const colorIconHtml = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25" height="100%" width="100%">
                <rect rx="2" x="1" y="1" width="22" height="22" fill="var(--fill-color)" stroke="var(--stroke-color)" style="stroke-width:2"></rect>
            </svg>
        `;

        return this._colors.map(function(color) {
            const entryColorIconHtml = colorIconHtml
                .replace('var(--fill-color)', color.fill || self._defaultFillColor)
                .replace('var(--stroke-color)', color.stroke || self._defaultStrokeColor);

            return {
                title: self._translate(color.label),
                id: color.label.toLowerCase() + '-color',
                imageHtml: entryColorIconHtml,
                action: function() {
                    self._modeling.setColor(element, color);
                }
            };
        });
    };

    ColorPopupProvider.$inject = ['eventBus', 'popupMenu', 'modeling', 'translate'];

    // ✅ ColorContextPadProvider
    function ColorContextPadProvider(contextPad, popupMenu, canvas, translate) {
        this._contextPad = contextPad;
        this._popupMenu = popupMenu;
        this._canvas = canvas;
        this._translate = translate || function(str) { return str; };

        contextPad.registerProvider(this);
    }

    ColorContextPadProvider.prototype.getContextPadEntries = function(element) {
        return this._createPopupAction([element]);
    };

    ColorContextPadProvider.prototype.getMultiElementContextPadEntries = function(elements) {
        return this._createPopupAction(elements);
    };

    ColorContextPadProvider.prototype._createPopupAction = function(elements) {
        var self = this;

        return {
            'set-color': {
                group: 'edit',
                className: 'bpmn-icon-color',
                title: this._translate('Set color'),
                html: '<div class="entry">' + colorImageSvg + '</div>',
                action: {
                    click: function(event, element) {
                        var position = getStartPosition(self._contextPad, elements);
                        position.cursor = {
                            x: event.x,
                            y: event.y
                        };

                        self._popupMenu.open(elements, 'color-picker', position);
                    }
                }
            }
        };
    };

    ColorContextPadProvider.$inject = ['contextPad', 'popupMenu', 'canvas', 'translate'];

    // ✅ Función auxiliar
    function getStartPosition(contextPad, elements) {
        var Y_OFFSET = 5;
        var pad = contextPad.getPad(elements).html;
        var padRect = pad.getBoundingClientRect();

        return {
            x: padRect.left,
            y: padRect.bottom + Y_OFFSET
        };
    }

    // ✅ Combinar ambos proveedores en un solo módulo (equivalente al index.js original)
    global.ColorPickerModule = {
        __init__: [
            'colorPopupProvider',
            'colorContextPadProvider'
        ],
        colorPopupProvider: ['type', ColorPopupProvider],
        colorContextPadProvider: ['type', ColorContextPadProvider]
    };

})(window);