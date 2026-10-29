
const datahub = document.querySelector('gn-datahub');

if (datahub) {
    const style = document.createElement('style');

    style.textContent = `
        gn-ui-autocomplete .gn-ui-text-input {
            border-radius: 3em!important;
        }

        gn-ui-button[_ngcontent-ng-c1983254442]:last-of-type {
            border-radius: 3em!important;
        }
        
        datahub-home-header div:has(> div > button.badge-btn) {
            justify-content: center;
        }

        datahub-record-header h1 {
            font-weight: 700;
            line-height:1.1;
        }

        .gn-ui-btn-light {
            border-top-right-radius: 3em;
            border-bottom-right-radius: 3em;
        }

        .omees-title {
            display: block;
            width: 100%;
            text-align: center;
            font-size: clamp(36px, 5vw, 58px);
            font-weight: 800;
            line-height: 0.95;
            letter-spacing: -0.04em;
            color: var(--color-primary);
        }

        .omees-title__highlight {
            display: inline-block;
            background: var(--color-secondary);
            color: white;
            padding: 0 12px 6px;
            border-radius: 100px;
            transform: rotate(-3deg);
            white-space: nowrap;
        }

        .badge-btn {
            border-radius: 1em !important;
        }
    `;

    const injectStyle = () => {
        const shadow = datahub.shadowRoot;

        if (!shadow) return false;

        if (!shadow.querySelector('#omees-datahub-style')) {
            style.id = 'omees-datahub-style';
            shadow.appendChild(style);
        }

        return true;
    };

    if (!injectStyle()) {
        customElements.whenDefined('gn-datahub').then(() => {
            injectStyle();
        });
    }
}
