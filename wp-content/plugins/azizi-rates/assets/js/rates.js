(() => {
    let data = null;
    let currentMode = 'card';

    //const API_URL = 'https://azizimoliya.tj/rates-api/';
    const API_URL = 'https://newsite.azizimoliya.tj/rates-api/'; 
    const ORDER = ['rub', 'eur', 'usd'];
    const META = {
        rub: { code: 'RUB', flag: '🇷🇺' },
        eur: { code: 'EUR', flag: '🇪🇺' },
        usd: { code: 'USD', flag: '🇺🇸' },
    };

    const bodyEl   = document.getElementById('cw-body');
    const modeBtn  = document.getElementById('cw-mode-btn');
    const modeLbl  = document.getElementById('cw-mode-label');
    const menuEl   = document.getElementById('cw-menu');
    const dateEl   = document.getElementById('cw-date');
    const noteEl   = document.getElementById('cw-note-mode');
    const amountEl = document.getElementById('cw-amount');
    const ccySel   = document.getElementById('cw-currency');
    const resultEl = document.getElementById('cw-result');

    const MODES = {
        nbt:    { label: cwL10n.labels.nbt,    buy: 'nbt',         sell: 'nbt' },
        card:   { label: cwL10n.labels.card,   buy: 'card_buy',    sell: 'card_sell' },
        mt:     { label: cwL10n.labels.mt,     buy: 'mt_buy',      sell: 'mt_sell' },
        beznal: { label: cwL10n.labels.beznal, buy: 'beznal_buy',  sell: 'beznal_sell' },
        kassa:  { label: cwL10n.labels.kassa,  buy: 'kassa_buy',   sell: 'kassa_sell' },
    };

    function fmt(val) {
        const n = Number(val);
        return isFinite(n) ? n.toFixed(4) : '—';
    }

    function renderTable() {
        if (!data) {
            bodyEl.innerHTML = `<tr><td colspan="3" class="cw-empty">${cwL10n.messages.empty}</td></tr>`;
            return;
        }
        const { buy: buyKey, sell: sellKey } = MODES[currentMode];
        const rows = ORDER.map(key => {
            const r = data[key];
            if (!r) return '';
            return `
                <tr>
                    <td>
                        <div class="cw-ccy">
                            <span class="cw-flag">${META[key].flag}</span>
                            <span class="cw-code">${META[key].code}</span>
                            <span class="cw-name">${cwL10n.currencies[key]}</span>
                        </div>
                    </td>
                    <td>${fmt(r[buyKey])}</td>
                    <td>${fmt(r[sellKey])}</td>
                </tr>
            `;
        }).join('');
        bodyEl.innerHTML = rows;
    }

    function renderConverter() {
        if (!data) { resultEl.textContent = '—'; return; }
        const { buy: buyKey } = MODES[currentMode];
        const ccy = ccySel.value;
        const rate = Number(data[ccy]?.[buyKey]);
        const amt = Number(amountEl.value);
        resultEl.textContent = isFinite(rate * amt) ? (rate * amt).toFixed(2) : '—';
    }

    function setMode(mode) {
        if (!MODES[mode]) return;
        currentMode = mode;
        modeLbl.textContent = MODES[mode].label;
        noteEl.textContent = MODES[mode].label.toLowerCase();
        renderTable();
        renderConverter();
    }

    // переключение меню
    modeBtn?.addEventListener('click', () => {
        const expanded = modeBtn.getAttribute('aria-expanded') === 'true';
        modeBtn.setAttribute('aria-expanded', String(!expanded));
        menuEl.setAttribute('aria-hidden', String(expanded));
        menuEl.classList.toggle('cw-open', !expanded);
    });

    menuEl?.addEventListener('click', e => {
        const btn = e.target.closest('.cw-menu-item');
        if (!btn) return;
        setMode(btn.dataset.mode);
        modeBtn.setAttribute('aria-expanded', 'false');
        menuEl.setAttribute('aria-hidden', 'true');
        menuEl.classList.remove('cw-open');
    });

    amountEl?.addEventListener('input', renderConverter);
    ccySel?.addEventListener('change', renderConverter);

    async function load() {
        try {
             const res = await fetch(API_URL, { cache: 'no-store' });
             data = await res.json();
           // const ratesStr = '{"rub":{"card_buy":"0.1188","card_sell":"0.1208","beznal_buy":"0.1171","beznal_sell":"0.1197","kassa_buy":"0.1186","kassa_sell":"0.1206","mt_buy":"0.1171","mt_sell":"0.1191","nbt":0.118,"date":"25.08.2025"},"eur":{"card_buy":"10.8","card_sell":"10.95","beznal_buy":"11.09","beznal_sell":"11.3","kassa_buy":"10.8","kassa_sell":"10.95","mt_buy":"10.8","mt_sell":"10.95","nbt":11.0885,"date":"25.08.2025"},"usd":{"card_buy":"9.5","card_sell":"9.67","beznal_buy":"9.5","beznal_sell":"9.67","kassa_buy":"9.5","kassa_sell":"9.67","mt_buy":"9.5","mt_sell":"9.67","nbt":9.5549,"date":"25.08.2025"}}';
            //data = JSON.parse(ratesStr);

            dateEl.textContent = data.usd?.date || '—';
            renderTable();
            renderConverter();
        } catch (err) {
            console.error('Rates load error:', err);
            bodyEl.innerHTML = `<tr><td colspan="3" class="cw-empty">${cwL10n.messages.error}</td></tr>`;
        }
    }

    setMode('card');
    load();
    setInterval(load, 5 * 60 * 1000);
})();
