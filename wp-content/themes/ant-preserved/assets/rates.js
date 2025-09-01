// (() => {
//     let inited = false;
//
//     function onDomReady(cb) {
//     if (document.readyState === 'loading') {
//     document.addEventListener('DOMContentLoaded', () => !inited && cb(), { once: true });
// } else {
//     if (!inited) cb();
// }
// }
//
//     onDomReady(() => {
//     inited = true;
//
//     const API_URL = 'https://azizimoliya.tj/rates-api/';
//
//     // Соответствие режимов —> полям API
//     const MODES = {
//     nbt:    { label: 'НБТ',                buy: 'nbt',         sell: 'nbt' },
//     card:   { label: 'По картам',          buy: 'card_buy',    sell: 'card_sell' },
//     mt:     { label: 'Денежных переводов', buy: 'mt_buy',      sell: 'mt_sell' },
//     beznal: { label: 'Безналичными',       buy: 'beznal_buy',  sell: 'beznal_sell' },
//     kassa:  { label: 'В кассе',            buy: 'kassa_buy',   sell: 'kassa_sell' },
// };
//
//     // Отрисовываемые валюты
//     const ORDER = ['rub','eur','usd'];
//     const META = {
//     rub: { code:'RUB', name:'Росс.рубль', flag:'🇷🇺' },
//     eur: { code:'EUR', name:'Евро',       flag:'🇪🇺' },
//     usd: { code:'USD', name:'Долл.США',   flag:'🇺🇸' },
// };
//
//     // DOM
//     const bodyEl   = document.getElementById('cw-body');
//     const modeBtn  = document.getElementById('cw-mode-btn');
//     const modeLbl  = document.getElementById('cw-mode-label');
//     const menuEl   = document.getElementById('cw-menu');
//     const dateEl   = document.getElementById('cw-date');
//     const noteEl   = document.getElementById('cw-note-mode');
//     const amountEl = document.getElementById('cw-amount');
//     const ccySel   = document.getElementById('cw-currency');
//     const resultEl = document.getElementById('cw-result');
//
//     let currentMode = 'card';
//     let data = null;
//
//     function fmt(val) {
//     const n = Number(val);
//     if (!isFinite(n)) return '—';
//     return n.toFixed(4);
// }
//
//     function fillDate() {
//     if (!data) { dateEl.textContent = '—'; return; }
//     const any = data.usd || data.eur || data.rub;
//     dateEl.textContent = any?.date || '—';
// }
//
//     function renderTable() {
//     if (!data) {
//     bodyEl.innerHTML = '<tr><td colspan="3" class="cw-empty">Нет данных</td></tr>';
//     return;
// }
//     const { buy:buyKey, sell:sellKey } = MODES[currentMode];
//
//     const rows = ORDER.map(key => {
//     const r = data[key];
//     if (!r) return '';
//     const buy  = r[buyKey];
//     const sell = r[sellKey];
//     return `
//           <tr>
//             <td>
//               <div class="cw-ccy">
//                 <span class="cw-flag">${META[key].flag}</span>
//                 <span class="cw-code">${META[key].code}</span>
//                 <span class="cw-name">${META[key].name}</span>
//               </div>
//             </td>
//             <td>${fmt(buy)}</td>
//             <td>${fmt(sell)}</td>
//           </tr>
//         `;
// }).join('');
//
//     bodyEl.innerHTML = rows;
// }
//
//     // Конвертер: из выбранной валюты в TJS по «Покупка»
//     function renderConverter() {
//     if (!data) { resultEl.textContent = '—'; return; }
//     const { buy: buyKey } = MODES[currentMode];
//     const ccy = ccySel.value;
//     const rate = Number(data[ccy]?.[buyKey]);
//     const amt  = Number(amountEl.value);
//     const out  = (isFinite(rate) && isFinite(amt)) ? (amt * rate) : NaN;
//     resultEl.textContent = isFinite(out) ? out.toFixed(2) : '—';
// }
//
//     function setMode(mode) {
//     if (!MODES[mode]) return;
//     currentMode = mode;
//     modeLbl.textContent = MODES[mode].label;
//     noteEl.textContent = MODES[mode].label.toLowerCase();
//     renderTable();
//     renderConverter();
// }
//
//     // UI: меню режимов
//     modeBtn?.addEventListener('click', () => {
//     const expanded = modeBtn.getAttribute('aria-expanded') === 'true';
//     modeBtn.setAttribute('aria-expanded', String(!expanded));
//     menuEl.setAttribute('aria-hidden', String(expanded));
// });
//
//     menuEl?.addEventListener('click', (e) => {
//     const btn = e.target.closest('.cw-menu-item');
//     if (!btn) return;
//     setMode(btn.dataset.mode);
//     modeBtn.setAttribute('aria-expanded', 'false');
//     menuEl.setAttribute('aria-hidden', 'true');
// });
//
//     document.addEventListener('click', (e) => {
//     const root = document.getElementById('cw-widget');
//     if (root && !root.contains(e.target)) {
//     modeBtn.setAttribute('aria-expanded', 'false');
//     menuEl.setAttribute('aria-hidden', 'true');
// }
// });
//
//     // Конвертер — события
//     amountEl?.addEventListener('input', renderConverter);
//     ccySel?.addEventListener('change', renderConverter);
//
//     // Загрузка данных
//     async function load() {
//     try {
//     // const res = await fetch(API_URL, { cache: 'no-store' });
//     // data = await res.json();
//     const ratesStr = '{"rub":{"ID":"11214","CURRENCY":"4","BUY":"0","SELL":"0","DATETIME":"2025-08-25 16:04:46","BUY_FIZ":"0","SELL_FIZ":"0","nbt":0.118,"card_buy":"0.1188","card_sell":"0.1208","beznal_buy":"0.1171","beznal_sell":"0.1197","kassa_buy":"0.1186","kassa_sell":"0.1206","mt_buy":"0.1171","mt_sell":"0.1191","tin_buy":"0.1171","tin_sell":"0.1191","currency_name":"rub","date":"25.08.2025"},"eur":{"ID":"11213","CURRENCY":"3","BUY":"0","SELL":"0","DATETIME":"2025-08-25 16:04:46","BUY_FIZ":"0","SELL_FIZ":"0","nbt":11.0885,"card_buy":"10.8","card_sell":"10.95","beznal_buy":"11.09","beznal_sell":"11.3","kassa_buy":"10.8","kassa_sell":"10.95","mt_buy":"10.8","mt_sell":"10.95","tin_buy":"10.8","tin_sell":"10.95","currency_name":"eur","date":"25.08.2025"},"usd":{"ID":"11212","CURRENCY":"2","BUY":"0","SELL":"0","DATETIME":"2025-08-25 16:04:46","BUY_FIZ":"0","SELL_FIZ":"0","nbt":9.5549,"card_buy":"9.5","card_sell":"9.67","beznal_buy":"9.5","beznal_sell":"9.67","kassa_buy":"9.5","kassa_sell":"9.67","mt_buy":"9.5","mt_sell":"9.67","tin_buy":"9.5","tin_sell":"9.67","currency_name":"usd","date":"25.08.2025"}}';
//     data = JSON.parse(ratesStr);
//
//     fillDate();
//     renderTable();
//     renderConverter();
// } catch (err) {
//     console.error('Rates load error:', err);
//     bodyEl.innerHTML = '<tr><td colspan="3" class="cw-empty">Ошибка загрузки</td></tr>';
// }
// }
//
//     // Старт
//     setMode('card');
//     load();
//     setInterval(load, 5 * 60 * 1000);
// });
// })();
