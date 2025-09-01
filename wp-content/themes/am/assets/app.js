
(() => {
  // Burger toggle
  const nav = document.getElementById('nav');
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action="burger"]');
    if (btn) nav.classList.toggle('open');
  });

  // Smooth scroll to anchors
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;
    const id = a.getAttribute('href').slice(1);
    const el = document.getElementById(id);
    if (!el) return;
    e.preventDefault();
    nav && nav.classList.remove('open');
    window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 70, behavior: 'smooth' });
  });

  // Reveal on scroll
  const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => { if (entry.isIntersecting) entry.target.classList.add('is-visible'); });
  }, { threshold: 0.15 });
  document.querySelectorAll('.reveal').forEach((el) => io.observe(el));

  // Accordion
  document.querySelectorAll('[data-accordion]').forEach((acc) => {
    const head = acc.querySelector('[data-acc-head]');
    if (head) head.addEventListener('click', () => acc.classList.toggle('open'));
  });

  // Minimal i18n (RU/TJ) — mirrors bundle logic (localStorage 'language')
  const dict = {
    ru: {
      shop: "Онлайн-магазин", service: "Центр обслуживания", connect: "Подключить",
      plans: "Пакеты", shopShort: "Магазин", news: "Новости", appBadge: "Приложение «Мой АНТ»",
      heroTitle: "Ваш цифровой сервис 24/7.", heroSubtitle: "Платежи, услуги и поддержка — в одном месте.",
      more: "Подробнее", support: "Поддержка", coverage: "Зона покрытия", channels: "Списки каналов",
      cabinet: "Личный кабинет", help: "Помощь", plansTitle: "Выберите пакет для подключения",
      plan1Title: "Пакет «Ичтимои 180»", plan1Desc: "Full HD каналы · архив 48 часов",
      plan2Title: "Пакет «Стандарт 180»", plan2Desc: "Архив просмотра до 48 часов",
      qTitle: "Остались вопросы?", qDesc: "Свяжитесь с техподдержкой 24/7.",
      shopTitle: "Доступно на shop.ant.tj", appTitle: "Мобильное приложение «Мой АНТ»",
      appDesc: "Наше приложение позволяет ...", faqTitle: "Частые вопросы",
      faq1: "Как подключиться к ANT?", faq2: "Сколько стоит оборудование?"
    },
    tj: {
      shop: "Магозини онлайн", service: "Маркази хизматрасонӣ", connect: "Пайваст",
      plans: "Бастаҳо", shopShort: "Магозин", news: "Хабарҳо", appBadge: "Барномаи «Мой АНТ»",
      heroTitle: "Хизматрасонии рақамии шумо 24/7.", heroSubtitle: "Пардохт, идоракунии хидматҳо ва дастгирӣ дар як ҷо.",
      more: "Муфассал", support: "Дастгирӣ", coverage: "Минтақаи фарогирӣ", channels: "Списки каналҳо",
      cabinet: "Личный кабинет", help: "Кумак", plansTitle: "Барои пайвастшавӣ бастаеро интихоб кунед",
      plan1Title: "«Ичтимои 180»", plan1Desc: "Full HD шабакаҳо · архив 48 соат",
      plan2Title: "«Стандарт 180»", plan2Desc: "Архив тамошо то 48 соат",
      qTitle: "Ҳанӯз саволҳо доред?", qDesc: "Бо дастгирии техникӣ 24/7 тамос гиред.",
      shopTitle: "Маълум дар shop.ant.tj", appTitle: "Заминаи мобилии «Мой АНТ»",
      appDesc: "Барномаи мо имкон медиҳад ...", faqTitle: "Саволҳои маъмул",
      faq1: "Чӣ тавр ба ANT пайваст шавам?", faq2: "Арзиши таҷҳизот чанд аст?"
    }
  };

  function setLang(lang){
    const d = dict[lang] || dict.tj;
    document.querySelectorAll("[data-i18n]").forEach((el) => {
      const key = el.getAttribute("data-i18n");
      if (d[key]) el.textContent = d[key];
    });
    localStorage.setItem("language", lang);
  }

  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-action='switch-lang']");
    if (!btn) return;
    setLang(btn.getAttribute("data-lang"));
  });

  // Init from storage (same key as bundle)
  setLang(localStorage.getItem("language") || "tj");
})();
