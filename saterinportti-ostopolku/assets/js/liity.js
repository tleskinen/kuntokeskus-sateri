/**
 * Säterinportti — ostopolun JS
 *
 * Hoitaa interaktiot kaikilla 4 sivulla:
 *  - /liity      → scope-valinta + commitment-kortit (dynaaminen renderöinti)
 *                 + ryhmäliikunta-modaali + info-ikoni
 *  - /jatka      → kirjautumisvalinta + tilauskooste
 *  - /maksu      → upsellit + maksutapa + tilauskooste päivittyy
 *  - /vahvistus  → tilauskooste (read-only)
 *
 * State pidetään sessionStoragessa, jotta valinnat säilyvät sivujen yli.
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'sp_ostopolku_state';
	var DATA = window.SP_OSTOPOLKU || null;

	function loadState() {
		try {
			var raw = sessionStorage.getItem(STORAGE_KEY);
			if (raw) return JSON.parse(raw);
		} catch (e) {}
		return {
			scope: 'gym-classes',
			commitment: 'etuasiakkuus',
			loginMode: 'guest',
			upsells: {},
			lisajasen: { added: false, firstname: '', lastname: '', email: '' },
			payment: 'card',
			customer: {
				email: '', firstname: '', lastname: '', phone: '',
				birthdate: '', gender: '',
				address: '', postalcode: '', city: '',
				accept_terms: false
				/* salasanaa EI tallenneta sessionStorageen tietoturvasyistä */
			}
		};
	}

	// Numero suomalaiseen muotoon
	function parseEUR(s) {
		// "30,83" → 30.83, "39" → 39
		if (typeof s === 'number') return s;
		return parseFloat(('' + s).replace(',', '.')) || 0;
	}

	function saveState(s) {
		try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(s)); } catch (e) {}
	}

	function fmt(n) {
		if (typeof n === 'string') return n;
		if (Number.isInteger(n)) return n.toString();
		return n.toFixed(2).replace('.', ',');
	}

	function commitmentByKey(key) {
		if (!DATA || !DATA.commits) return null;
		for (var i = 0; i < DATA.commits.length; i++) {
			if (DATA.commits[i].key === key) return DATA.commits[i];
		}
		return null;
	}

	function pricingFor(scope, commitKey) {
		if (!DATA || !DATA.pricing || !DATA.pricing[scope]) return null;
		return DATA.pricing[scope][commitKey] || null;
	}

	function tarjousAppliesTo(commitKey) {
		var t = DATA && DATA.tarjous;
		if (!t || !t.active) return false;
		return Array.isArray(t.applies_to) && t.applies_to.indexOf(commitKey) !== -1;
	}

	/**
	 * Rakentaa WC checkout -URL:n valitulle scope+commitment-kombinaatiolle.
	 * Käyttää tarjous-tuote-ID:tä jos tarjous koskee, muuten vakio-ID:tä.
	 * Lisää lisäjäsen-tiedot URL-parametreina (sp_friend_*) jotta WC-puolen koodi
	 * voi käsitellä toisen jäsenen luonnin (manuaalisesti tai webhookilla).
	 */
	function buildPayUrl(state) {
		if (!DATA || !DATA.pricing) return '#';
		var p = pricingFor(state.scope, state.commitment);
		if (!p) return '#';

		var tarjousActive = tarjousAppliesTo(state.commitment);
		var pid;
		if (tarjousActive && p.tarjous_product_id) {
			pid = p.tarjous_product_id;
		} else if (p.product_id) {
			pid = p.product_id;
		} else if (p.tarjous_product_id) {
			pid = p.tarjous_product_id; // fallback (esim. sali-vuosikortti, jossa vakiota ei ole)
		} else {
			return '#';
		}

		var base = DATA.checkoutUrl || '/checkout/';
		var sep  = base.indexOf('?') === -1 ? '?' : '&';
		var url  = base + sep + 'add-to-cart=' + pid;

		// Lisäjäsen-data WC:lle. Säterinportin WC/FB-integraation pitää lukea
		// nämä parametrit ja luoda toinen asiakkuus.
		if (state.lisajasen && state.lisajasen.added) {
			url += '&sp_friend=1';
			if (state.lisajasen.firstname) url += '&sp_friend_first=' + encodeURIComponent(state.lisajasen.firstname);
			if (state.lisajasen.lastname)  url += '&sp_friend_last='  + encodeURIComponent(state.lisajasen.lastname);
			if (state.lisajasen.email)     url += '&sp_friend_email=' + encodeURIComponent(state.lisajasen.email);
		}

		// Upsellit (kertaluonteiset): Säterinporttia varten lisätään tilauksen meta-tiedoiksi
		if (state.upsells && Object.keys(state.upsells).length) {
			url += '&sp_upsells=' + encodeURIComponent(Object.keys(state.upsells).join(','));
		}

		// Maksutapa-vihje (Säterinportti voi esiasetalla maksutavan WC-kassalla)
		if (state.payment) url += '&sp_payment=' + encodeURIComponent(state.payment);

		return url;
	}

	// === /liity: scope + commitment-kortit ===
	function renderLiity(state) {
		var cardsEl = document.getElementById('sp-cards');
		if (!cardsEl || !DATA) return;

		cardsEl.innerHTML = '';
		DATA.commits.forEach(function (c) {
			var p = pricingFor(state.scope, c.key);
			if (!p) return;

			var article = document.createElement('article');
			article.className = 'sp-card' + (state.commitment === c.key ? ' is-selected' : '');
			article.setAttribute('role', 'radio');
			article.setAttribute('aria-checked', state.commitment === c.key ? 'true' : 'false');
			article.setAttribute('tabindex', '0');
			article.setAttribute('data-commitment', c.key);

			// Header
			var hdr = document.createElement('div');
			hdr.className = 'sp-card-header';
			var hdrTxt = document.createElement('div');
			var h2 = document.createElement('h2');
			h2.textContent = c.name;
			var sub = document.createElement('p');
			sub.className = 'sp-card-subtitle';
			sub.textContent = c.subtitle;
			hdrTxt.appendChild(h2); hdrTxt.appendChild(sub);
			var radio = document.createElement('span');
			radio.className = 'sp-card-radio';
			radio.setAttribute('aria-hidden', 'true');
			hdr.appendChild(hdrTxt); hdr.appendChild(radio);
			article.appendChild(hdr);

			// Hinta
			var monthlyShown = c.key === 'vuosisaasto' ? p.monthly_equiv : p.monthly;
			var yearlyShown  = p.total_year || '';
			var tarjous = tarjousAppliesTo(c.key);

			var price = document.createElement('p');
			price.className = 'sp-card-price';
			var sub2 = document.createElement('p');
			sub2.className = 'sp-card-price-sub';

			if (tarjous) {
				var t = DATA.tarjous;
				price.innerHTML =
					'<span class="sp-card-price-was">' + fmt(monthlyShown) + ' €</span>' +
					'<span>' + t.first_month_price + ' € / kk<sup class="sp-tarjous-star">*</sup></span>';
				sub2.textContent = 'Jatkuu ' + fmt(monthlyShown) + ' €/kk' +
					(yearlyShown ? ' (' + fmt(yearlyShown) + ' €/v)' : '');
			} else if (c.key === 'vuosisaasto') {
				price.textContent = fmt(monthlyShown) + ' € / kk';
				sub2.textContent = 'Kokonaishinta ' + fmt(yearlyShown) + ' €';
			} else if (c.key === 'etuasiakkuus') {
				price.textContent = fmt(monthlyShown) + ' € / kk';
				sub2.textContent = 'Vuodessa ' + fmt(yearlyShown) + ' €';
			} else {
				price.textContent = fmt(monthlyShown) + ' € / kk';
				sub2.innerHTML = '&nbsp;';
			}
			article.appendChild(price);
			article.appendChild(sub2);

			// Pillit
			if (c.pill_accent || c.pill_neutral) {
				var pills = document.createElement('div');
				pills.className = 'sp-card-pills';
				if (c.pill_accent) {
					var pa = document.createElement('span');
					pa.className = 'sp-pill sp-pill-accent';
					pa.textContent = c.pill_accent;
					pills.appendChild(pa);
				}
				if (c.pill_neutral) {
					var pn = document.createElement('span');
					pn.className = 'sp-pill sp-pill-neutral';
					pn.textContent = c.pill_neutral;
					pills.appendChild(pn);
				}
				article.appendChild(pills);
			}

			article.appendChild(document.createElement('hr')).className = 'sp-card-divider';

			// Features (commit_note ensimmäisenä, ei korostusta)
			var ul = document.createElement('ul');
			ul.className = 'sp-card-features';
			var all = c.commit_note ? [c.commit_note].concat(c.features || []) : (c.features || []);
			all.forEach(function (f) {
				var li = document.createElement('li');
				var check = document.createElement('span');
				check.className = 'sp-check';
				check.textContent = '✓';
				var txt = document.createElement('span');
				txt.textContent = f;
				li.appendChild(check); li.appendChild(txt);
				ul.appendChild(li);
			});
			article.appendChild(ul);

			article.addEventListener('click', function () {
				state.commitment = c.key;
				saveState(state);
				renderLiity(state);
			});
			article.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					state.commitment = c.key;
					saveState(state);
					renderLiity(state);
				}
			});

			cardsEl.appendChild(article);
		});

		// Scope-elementtien aktiivinen tila
		document.querySelectorAll('.sp-scope-option').forEach(function (opt) {
			var selected = opt.getAttribute('data-scope') === state.scope;
			opt.classList.toggle('is-selected', selected);
			opt.setAttribute('aria-checked', selected ? 'true' : 'false');
		});
	}

	function wireLiityScope(state) {
		document.querySelectorAll('.sp-scope-option').forEach(function (opt) {
			function pick() {
				state.scope = opt.getAttribute('data-scope');
				saveState(state);
				renderLiity(state);
			}
			opt.addEventListener('click', function (e) {
				if (e.target.closest('.sp-info-icon')) return;
				pick();
			});
			opt.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(); }
			});
		});
	}

	// === Ryhmäliikunta-modaali (käytössä /liity-sivulla) ===
	function wireClassesModal() {
		var modal = document.getElementById('sp-classes-modal');
		var closeBtn = document.getElementById('sp-classes-modal-close');
		if (!modal) return;

		function open() {
			if (typeof modal.showModal === 'function') modal.showModal();
			else modal.setAttribute('open', '');
		}

		document.querySelectorAll('[data-classes-open]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault(); e.stopPropagation();
				open();
			});
		});
		if (closeBtn) closeBtn.addEventListener('click', function () { modal.close(); });
		modal.addEventListener('click', function (e) {
			var rect = modal.getBoundingClientRect();
			var inside = e.clientX >= rect.left && e.clientX <= rect.right
			          && e.clientY >= rect.top && e.clientY <= rect.bottom;
			if (!inside) modal.close();
		});
	}

	// === /jatka: yhteystiedot-lomake (kaikki kentät) ===
	// Kentät on FB:n vaatimuksesta pakollisia rekisteröintihetkellä — UX-puolen
	// keinot: selkeä ryhmittely, autocomplete-attribuutit (selaimen autofill),
	// natiivi date-picker, sukupuoli-pillit, billing-toggle.
	var CUSTOMER_FIELDS = [
		'email', 'firstname', 'lastname', 'phone',
		'birthdate',
		'address', 'postalcode', 'city',
		'guardian_name', 'guardian_email', 'guardian_address',
		'guardian_postalcode', 'guardian_city'
	];

	function ensureCustomer(state) {
		if (!state.customer) {
			state.customer = {
				email: '', firstname: '', lastname: '', phone: '',
				birthdate: '', gender: '',
				address: '', postalcode: '', city: '',
				guardian_name: '', guardian_email: '', guardian_address: '',
				guardian_postalcode: '', guardian_city: '',
				accept_terms: false
			};
		}
		return state.customer;
	}

	// Laskee iän syntymäpäivän perusteella. Palauttaa null jos arvo ei ole
	// kelvollinen päivä (esim. tyhjä tai keskeneräinen).
	function calculateAge(dateStr) {
		if (!dateStr) return null;
		var birth = new Date(dateStr);
		if (isNaN(birth.getTime())) return null;
		var today = new Date();
		var age = today.getFullYear() - birth.getFullYear();
		var m = today.getMonth() - birth.getMonth();
		if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
		return age;
	}

	function wireCustomerForm(state) {
		var form = document.getElementById('sp-guest-form');
		if (!form) return;
		var c = ensureCustomer(state);

		// Esitäyttö sessionStorage-tilasta
		CUSTOMER_FIELDS.forEach(function (name) {
			var id = 'sp-' + name.replace(/_/g, '-');
			var el = document.getElementById(id);
			if (el && c[name]) el.value = c[name];
		});

		// Sukupuoli (radio)
		if (c.gender) {
			var g = form.querySelector('input[name="gender"][value="' + c.gender + '"]');
			if (g) g.checked = true;
		}

		// Esitäyttö: käyttöehdot-checkbox
		var termsCb = document.getElementById('sp-accept-terms');
		if (termsCb && c.accept_terms) termsCb.checked = true;

		// Tallennus inputeista
		form.querySelectorAll('input').forEach(function (input) {
			var name = input.name;
			if (!name) return;

			// Salasanaa ei tallenneta sessionStorageen tietoturvasyistä
			if (input.type === 'password') return;

			if (input.type === 'radio') {
				input.addEventListener('change', function () {
					if (input.checked) {
						c[name] = input.value;
						saveState(state);
					}
				});
			} else if (input.type === 'checkbox') {
				input.addEventListener('change', function () {
					c[name] = input.checked;
					saveState(state);
				});
			} else {
				input.addEventListener('input', function () {
					c[name] = input.value;
					saveState(state);
				});
			}
		});
	}

	// === Lomakkeen validointi (taso 2: HTML5-säännöt + suomenkieliset inline-virheet) ===
	var REQUIRED_MSG = {
		email: 'Anna sähköpostiosoite.',
		firstname: 'Anna etunimi.',
		lastname: 'Anna sukunimi.',
		phone: 'Anna puhelinnumero.',
		birthdate: 'Anna syntymäaika.',
		gender: 'Valitse sukupuoli.',
		address: 'Anna katuosoite.',
		postalcode: 'Anna postinumero.',
		city: 'Anna postitoimipaikka.',
		password: 'Anna salasana.',
		password_confirm: 'Vahvista salasana.',
		accept_terms: 'Hyväksy käyttöehdot jatkaaksesi.',
		guardian_name: 'Anna huoltajan nimi (laskun saaja).',
		guardian_email: 'Anna huoltajan sähköposti.',
		guardian_address: 'Anna huoltajan katuosoite.',
		guardian_postalcode: 'Anna huoltajan postinumero.',
		guardian_city: 'Anna huoltajan postitoimipaikka.'
	};

	// Palauttaa virheviestin tai null jos kenttä on validi
	function validateInput(input) {
		var name = input.name;

		// Skippaa kentät jotka eivät ole näkyvissä (esim. huoltaja-sektio aikuiselle,
		// tai login-kentät guest-tilassa)
		if (input.closest('[hidden]')) return null;
		if (input.offsetParent === null && input.type !== 'hidden') return null;

		// Checkbox (käyttöehdot)
		if (input.type === 'checkbox') {
			if (input.required && !input.checked) return REQUIRED_MSG[name] || 'Tämä kenttä on pakollinen.';
			return null;
		}

		// Radio (sukupuoli) — riittää että ryhmästä on yksi valittu
		if (input.type === 'radio') {
			if (!input.required) return null;
			var group = document.querySelectorAll('input[type="radio"][name="' + name + '"]');
			var anyChecked = false;
			group.forEach(function (r) { if (r.checked) anyChecked = true; });
			return anyChecked ? null : (REQUIRED_MSG[name] || 'Valitse vaihtoehto.');
		}

		var value = (input.value || '').trim();

		// Pakollisuus
		if (input.required && !value) return REQUIRED_MSG[name] || 'Tämä kenttä on pakollinen.';
		if (!value) return null;

		// Tyyppi-spesifiset säännöt
		if (input.type === 'email') {
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Tarkista sähköpostiosoite.';
		}

		if (input.type === 'tel') {
			var digits = value.replace(/[^0-9]/g, '');
			if (digits.length < 7) return 'Tarkista puhelinnumero — vähintään 7 numeroa.';
		}

		if (input.type === 'date' && name === 'birthdate') {
			var birth = new Date(value);
			if (isNaN(birth.getTime())) return 'Tarkista päivämäärä.';
			if (birth > new Date()) return 'Syntymäaika ei voi olla tulevaisuudessa.';
			var age = calculateAge(value);
			if (age !== null && age < 15) return 'Säterinportin jäseneksi voi liittyä 15-vuotiaasta alkaen.';
			if (age !== null && age > 120) return 'Tarkista syntymäaika.';
		}

		if (input.type === 'password') {
			var min = parseInt(input.getAttribute('minlength'), 10);
			if (min > 0 && value.length < min) {
				return 'Salasana vähintään ' + min + ' merkkiä.';
			}
			if (name === 'password_confirm') {
				var pw = document.getElementById('sp-password');
				if (pw && value !== pw.value) return 'Salasanat eivät täsmää.';
			}
		}

		// Pattern (esim. postinumero)
		var pattern = input.getAttribute('pattern');
		if (pattern) {
			var re = new RegExp('^(?:' + pattern + ')$');
			if (!re.test(value)) {
				if (/postalcode$/.test(name)) return 'Postinumero on 5 numeroa, esim. 02600.';
				return 'Tarkista kentän muoto.';
			}
		}

		return null;
	}

	// Hakee tai luo virheviesti-elementin kentälle
	function getErrorEl(input) {
		var key = input.name || input.id;
		var id = 'sp-error-' + key.replace(/_/g, '-');
		var existing = document.getElementById(id);
		if (existing) return existing;

		var p = document.createElement('p');
		p.className = 'sp-form-error';
		p.id = id;
		p.hidden = true;

		// Sijoitus: checkboxille .sp-checkbox-row:n jälkeen, muille .sp-form-field:n loppuun
		if (input.type === 'checkbox') {
			var row = input.closest('.sp-checkbox-row');
			if (row && row.parentElement) row.parentElement.insertBefore(p, row.nextSibling);
			else input.parentElement.appendChild(p);
		} else {
			var field = input.closest('.sp-form-field');
			if (field) field.appendChild(p);
			else input.parentElement.appendChild(p);
		}
		return p;
	}

	function setError(input, message) {
		var errEl = getErrorEl(input);
		if (message) {
			errEl.textContent = message;
			errEl.hidden = false;
			input.setAttribute('aria-invalid', 'true');
			input.setAttribute('aria-describedby', errEl.id);
			// Radio-rivi: merkitse koko ryhmän container
			if (input.type === 'radio') {
				var row = input.closest('.sp-radio-row');
				if (row) row.setAttribute('aria-invalid', 'true');
			}
		} else {
			errEl.hidden = true;
			errEl.textContent = '';
			input.removeAttribute('aria-invalid');
			if (input.type === 'radio') {
				var row2 = input.closest('.sp-radio-row');
				if (row2) row2.removeAttribute('aria-invalid');
			}
		}
	}

	function wireValidation() {
		var form = document.getElementById('sp-jatka-form');
		if (!form) return;

		var inputs = form.querySelectorAll('input');

		inputs.forEach(function (input) {
			// Blur: tarkista kun käyttäjä lähtee kentästä
			input.addEventListener('blur', function () {
				setError(input, validateInput(input));
			});
			// Input/change: nollaa virhe heti kun kenttä korjautuu
			var evt = (input.type === 'checkbox' || input.type === 'radio') ? 'change' : 'input';
			input.addEventListener(evt, function () {
				if (input.hasAttribute('aria-invalid')) {
					var msg = validateInput(input);
					if (!msg) setError(input, null);
				}
				// Salasana-kentän muutos vaikuttaa myös vahvistuskenttään
				if (input.id === 'sp-password') {
					var confirm = document.getElementById('sp-password-confirm');
					if (confirm && confirm.hasAttribute('aria-invalid')) {
						setError(confirm, validateInput(confirm));
					}
				}
			});
		});

		// Submit: validoi kaikki, navigoi vain jos OK
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var firstInvalid = null;
			var seenRadioGroups = {};

			inputs.forEach(function (input) {
				// Radio-ryhmästä validoidaan vain kerran (käyttäen ensimmäistä)
				if (input.type === 'radio') {
					if (seenRadioGroups[input.name]) return;
					seenRadioGroups[input.name] = true;
				}
				var msg = validateInput(input);
				setError(input, msg);
				if (msg && !firstInvalid) firstInvalid = input;
			});

			if (firstInvalid) {
				firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
				setTimeout(function () { firstInvalid.focus({ preventScroll: true }); }, 300);
				return;
			}

			// Kaikki OK → navigoi /maksu-sivulle
			var btn = form.querySelector('.sp-continue-btn');
			var url = btn && btn.getAttribute('data-maksu-url');
			if (url) window.location.href = url;
		});
	}

	// Huoltajan laskutustiedot — näkyvät vain kun syntymäaika on alle 18 v
	function wireGuardianFields(state) {
		var birthdateEl = document.getElementById('sp-birthdate');
		var section = document.getElementById('sp-guardian-section');
		if (!birthdateEl || !section) return;

		var fields = section.querySelectorAll('input');
		var c = ensureCustomer(state);

		function update() {
			var age = calculateAge(birthdateEl.value);
			var isMinor = age !== null && age >= 0 && age < 18;
			section.hidden = !isMinor;
			fields.forEach(function (input) {
				if (isMinor) {
					input.setAttribute('required', '');
				} else {
					input.removeAttribute('required');
				}
			});
		}

		birthdateEl.addEventListener('input', update);
		birthdateEl.addEventListener('change', update);
		update(); // alkutila — esitäytetyn päivän mukaan
	}

	// Salasana-kentän näytä/piilota-toggle
	function wirePasswordToggles() {
		document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
			var id = btn.getAttribute('data-password-toggle');
			var input = id ? document.getElementById(id) : null;
			if (!input) return;
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var visible = input.type === 'text';
				input.type = visible ? 'password' : 'text';
				btn.setAttribute('aria-pressed', visible ? 'false' : 'true');
				btn.setAttribute('aria-label', visible ? 'Näytä salasana' : 'Piilota salasana');
			});
		});
	}

	// Yleinen info-ikoni → näytä/piilota selitys-laatikko
	// Käytä: <button data-info-toggle aria-controls="ID">i</button>
	//       <div id="ID" hidden>...<button data-info-close="ID">×</button></div>
	function wireInfoToggles() {
		document.querySelectorAll('[data-info-toggle]').forEach(function (btn) {
			var id = btn.getAttribute('aria-controls');
			var target = id ? document.getElementById(id) : null;
			if (!target) return;
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var open = btn.getAttribute('aria-expanded') === 'true';
				btn.setAttribute('aria-expanded', open ? 'false' : 'true');
				target.hidden = open;
			});
		});

		// X-sulkin hint-laatikon sisällä
		document.querySelectorAll('[data-info-close]').forEach(function (close) {
			close.addEventListener('click', function (e) {
				e.preventDefault();
				var id = close.getAttribute('data-info-close');
				var hint = document.getElementById(id);
				if (hint) hint.hidden = true;
				var toggle = document.querySelector('[data-info-toggle][aria-controls="' + id + '"]');
				if (toggle) {
					toggle.setAttribute('aria-expanded', 'false');
					toggle.focus();
				}
			});
		});
	}

	// === /jatka: login-valinta ===
	function wireRekisterointi(state) {
		var options = document.querySelectorAll('#sp-login-choice .sp-login-option');
		var loginForm = document.getElementById('sp-login-form');
		var guestForm = document.getElementById('sp-guest-form');

		function setMode(mode) {
			state.loginMode = mode;
			saveState(state);
			options.forEach(function (opt) {
				var active = opt.getAttribute('data-mode') === mode;
				opt.classList.toggle('is-active', active);
				opt.setAttribute('aria-checked', active ? 'true' : 'false');
			});
			if (loginForm) loginForm.classList.toggle('is-open', mode === 'login');
			if (guestForm) guestForm.style.display = mode === 'guest' ? '' : 'none';
		}
		options.forEach(function (opt) {
			opt.addEventListener('click', function () { setMode(opt.getAttribute('data-mode')); });
		});
		setMode(state.loginMode || 'guest');
	}

	// Lisäjäsenen hinta (commitment-hinta − discount %).
	// Palauttaa numeroinen arvo (esim. 54.40), ja näytön muoto erikseen.
	function friendMonthly(state) {
		var p = pricingFor(state.scope, state.commitment);
		if (!p) return 0;
		var base = state.commitment === 'vuosisaasto' ? parseEUR(p.monthly_equiv) : parseEUR(p.monthly);
		var card = document.querySelector('.sp-upsell-friend');
		var disc = card ? parseInt(card.getAttribute('data-discount'), 10) : 15;
		return base * (1 - disc / 100);
	}

	function friendTarjousApplies(state) {
		if (!tarjousAppliesTo(state.commitment)) return false;
		var card = document.querySelector('.sp-upsell-friend');
		if (!card) return false;
		return card.getAttribute('data-tarjous-applies') === '1';
	}

	function friendAvainkorttiFree(state) {
		var card = document.querySelector('.sp-upsell-friend');
		if (!card) return false;
		return card.getAttribute('data-avainkortti-free') === '1' && tarjousAppliesTo(state.commitment);
	}

	// === Tilauskooste (jaettu /jatka, /maksu, /vahvistus) ===
	function renderSummary(state) {
		var nameEl = document.getElementById('sp-sum-name');
		var scopeEl = document.getElementById('sp-sum-scope');
		var rowsEl = document.getElementById('sp-sum-rows');
		var totalEl = document.getElementById('sp-sum-total');
		var payBtn = document.getElementById('sp-pay-btn');
		if (!rowsEl || !totalEl) return;

		var commit = commitmentByKey(state.commitment);
		var p = pricingFor(state.scope, state.commitment);
		if (!commit || !p) return;

		var monthlyShown = state.commitment === 'vuosisaasto' ? p.monthly_equiv : p.monthly;
		var yearlyShown  = p.total_year || '';
		var tarjous = tarjousAppliesTo(state.commitment);
		var hasFriend = state.lisajasen && state.lisajasen.added;

		// Tuote-otsikko
		if (nameEl) nameEl.textContent = commit.name;
		if (scopeEl) {
			var scopeLabel = state.scope === 'gym-classes' ? 'Kuntosali + jumpat' : 'Kuntosali';
			scopeEl.textContent = scopeLabel + ' · ' + commit.subtitle.toLowerCase();
		}

		// Rivit
		rowsEl.innerHTML = '';

		// 1. Jäsenyys (asiakas)
		var jasenyysRow = document.createElement('div');
		jasenyysRow.className = 'sp-summary-row';
		var jasenyysLeft = document.createElement('div');
		jasenyysLeft.innerHTML =
			'<div class="sp-summary-row-label">Jäsenyys</div>' +
			(tarjous
				? '<div class="sp-summary-row-meta">1. kuukausi tarjoushinnalla<span class="sp-tarjous-star">*</span></div>'
				: '<div class="sp-summary-row-meta">' + commit.subtitle + '</div>');
		var jasenyysRight = document.createElement('div');
		jasenyysRight.className = 'sp-summary-row-price';
		if (tarjous) {
			var tval = DATA.tarjous.first_month_price;
			jasenyysRight.innerHTML =
				'<span class="sp-was">' + fmt(monthlyShown) + ' €</span>' +
				'<span class="sp-now">' + tval + ' €</span>';
		} else {
			jasenyysRight.innerHTML = '<span class="sp-now">' + fmt(monthlyShown) + ' €</span>';
		}
		jasenyysRow.appendChild(jasenyysLeft); jasenyysRow.appendChild(jasenyysRight);
		rowsEl.appendChild(jasenyysRow);

		// 2. Lisäjäsen-rivi (jos lisätty)
		var friendAmount = 0;
		if (hasFriend) {
			var friendRow = document.createElement('div');
			friendRow.className = 'sp-summary-row';
			var friendName = (state.lisajasen.firstname || state.lisajasen.lastname)
				? (state.lisajasen.firstname + ' ' + state.lisajasen.lastname).trim()
				: 'Kaveri';
			var fTarjous = friendTarjousApplies(state);
			var fMonthly = friendMonthly(state);
			var friendPriceHtml;
			if (fTarjous) {
				friendAmount = DATA.tarjous.first_month_price;
				friendPriceHtml =
					'<span class="sp-was">' + fmt(fMonthly) + ' €</span>' +
					'<span class="sp-now">' + DATA.tarjous.first_month_price + ' €</span>';
			} else {
				friendAmount = fMonthly;
				friendPriceHtml = '<span class="sp-now">' + fmt(fMonthly) + ' €</span>';
			}
			friendRow.innerHTML =
				'<div>' +
					'<div class="sp-summary-row-label">Lisäjäsen — ' + friendName + '</div>' +
					'<div class="sp-summary-row-meta">' + (fTarjous ? '1. kk tarjoushinnalla — sitten −15 %' : 'Kaveri-alennus −15 %') + '</div>' +
				'</div>' +
				'<div class="sp-summary-row-price">' + friendPriceHtml + '</div>';
			rowsEl.appendChild(friendRow);
		}

		// 3. Avainkortti (asiakas)
		var avainRow = document.createElement('div');
		avainRow.className = 'sp-summary-row';
		var avainFree = tarjous && DATA.tarjous.avainkortti_free;
		avainRow.innerHTML =
			'<div>' +
				'<div class="sp-summary-row-label">Avainkortti' + (hasFriend ? ' (omasi)' : '') + '</div>' +
				'<div class="sp-summary-row-meta">' + (avainFree ? 'Tarjouksen aikana veloituksetta' : 'Kertamaksu, noudetaan aulapalvelusta') + '</div>' +
			'</div>' +
			'<div class="sp-summary-row-price">' +
				(avainFree ? '<span class="sp-was">15 €</span><span class="sp-now">0 €</span>' : '<span class="sp-now">15 €</span>') +
			'</div>';
		rowsEl.appendChild(avainRow);

		// 4. Avainkortti (kaverin)
		var friendAvainAmount = 0;
		if (hasFriend) {
			var fAvainFree = friendAvainkorttiFree(state);
			var friendAvainRow = document.createElement('div');
			friendAvainRow.className = 'sp-summary-row';
			friendAvainRow.innerHTML =
				'<div>' +
					'<div class="sp-summary-row-label">Avainkortti (kaverin)</div>' +
					'<div class="sp-summary-row-meta">' + (fAvainFree ? 'Tarjouksen aikana veloituksetta' : 'Kertamaksu') + '</div>' +
				'</div>' +
				'<div class="sp-summary-row-price">' +
					(fAvainFree ? '<span class="sp-was">15 €</span><span class="sp-now">0 €</span>' : '<span class="sp-now">15 €</span>') +
				'</div>';
			rowsEl.appendChild(friendAvainRow);
			friendAvainAmount = fAvainFree ? 0 : 15;
		}

		// 5. Upsellit (kertaluonteiset)
		var upsellTotal = 0;
		Object.keys(state.upsells || {}).forEach(function (id) {
			var price = state.upsells[id];
			var article = document.querySelector('.sp-upsell[data-id="' + id + '"]');
			var name = article ? article.querySelector('.sp-upsell-name').textContent : id;
			var row = document.createElement('div');
			row.className = 'sp-summary-row';
			row.innerHTML =
				'<div><div class="sp-summary-row-label">' + name + '</div><div class="sp-summary-row-meta">Kertaluonteinen</div></div>' +
				'<div class="sp-summary-row-price"><span class="sp-now">' + fmt(price) + ' €</span></div>';
			rowsEl.appendChild(row);
			upsellTotal += price;
		});

		// Yhteensä
		var jasenyysAmount = tarjous ? DATA.tarjous.first_month_price : parseEUR(monthlyShown);
		var avainAmount = avainFree ? 0 : 15;
		var total = jasenyysAmount + avainAmount + friendAmount + friendAvainAmount + upsellTotal;

		totalEl.textContent = fmt(total) + ' €';
		if (payBtn) {
			payBtn.textContent = 'Maksa ' + fmt(total) + ' €';
			// Päivitä WC-checkout-URL valittujen tuotteiden mukaan
			var url = buildPayUrl(state);
			if (url !== '#' && payBtn.tagName === 'A') {
				payBtn.setAttribute('href', url);
			}
		}

		// Sopimusehto-rivi yhteenvedon alla — riippuu valitusta sopimustyypistä
		// JA tarjouksen tilasta. Esim. Etuasiakkuus = "12 kk sitoumus, jatkuu joustavasti",
		// Joustojäsenyys = "Peruutus milloin vain". commit_note tulee class-packages.php:stä.
		var noteEl = document.getElementById('sp-sum-note');
		if (noteEl) {
			var parts = [];
			if (tarjous) {
				parts.push('<span class="sp-tarjous-star">*</span> Sen jälkeen normaali kk-hinta.');
			}
			if (commit.commit_note) {
				parts.push(commit.commit_note + '.');
			}
			noteEl.innerHTML = parts.join(' ') || '&nbsp;';
		}

		// Trust-line ensimmäinen kohta /maksu-sivulla: sopimusehto totuudenmukaisesti
		var trustEl = document.getElementById('sp-trust-commit');
		if (trustEl && commit.commit_note) {
			trustEl.textContent = commit.commit_note;
		}
	}

	function wireUpsells(state) {
		document.querySelectorAll('.sp-upsell').forEach(function (article) {
			if (article.classList.contains('sp-upsell-friend')) return; // hoidetaan erikseen
			var btn = article.querySelector('.sp-upsell-add');
			var id = article.getAttribute('data-id');
			var price = parseFloat(article.getAttribute('data-price'));
			if (state.upsells && id in state.upsells) {
				article.classList.add('is-added');
				btn.textContent = 'Lisätty';
			}
			btn.addEventListener('click', function () {
				if (!state.upsells) state.upsells = {};
				if (id in state.upsells) {
					delete state.upsells[id];
					article.classList.remove('is-added');
					btn.textContent = 'Lisää';
				} else {
					state.upsells[id] = price;
					article.classList.add('is-added');
					btn.textContent = 'Lisätty';
				}
				saveState(state);
				renderSummary(state);
			});
		});
	}

	function renderFriendPrice(state) {
		var card = document.querySelector('.sp-upsell-friend');
		var priceEl = document.getElementById('sp-friend-price');
		if (!card || !priceEl) return;
		var disc = parseInt(card.getAttribute('data-discount'), 10);
		var p = pricingFor(state.scope, state.commitment);
		if (!p) return;
		var base = state.commitment === 'vuosisaasto' ? parseEUR(p.monthly_equiv) : parseEUR(p.monthly);
		var discountedMonthly = base * (1 - disc / 100);
		priceEl.innerHTML =
			'<span class="sp-upsell-discount-pill">−' + disc + ' %</span>' +
			'<span class="sp-upsell-was">' + fmt(base) + ' €/kk</span>' +
			'<span class="sp-upsell-now">' + fmt(discountedMonthly) + ' €/kk</span>';
	}

	function wireLisajasen(state) {
		var card = document.querySelector('.sp-upsell-friend');
		if (!card) return;

		var btn = card.querySelector('.sp-upsell-add');
		var form = card.querySelector('.sp-friend-form');

		// Tilan palautus
		if (state.lisajasen && state.lisajasen.added) {
			card.classList.add('is-added');
			btn.textContent = 'Lisätty';
			if (form) form.hidden = false;
			['firstname', 'lastname', 'email'].forEach(function (key) {
				var input = card.querySelector('[data-friend-field="' + key + '"]');
				if (input && state.lisajasen[key]) input.value = state.lisajasen[key];
			});
		}

		btn.addEventListener('click', function () {
			if (!state.lisajasen) state.lisajasen = { added: false, firstname: '', lastname: '', email: '' };
			state.lisajasen.added = ! state.lisajasen.added;
			card.classList.toggle('is-added', state.lisajasen.added);
			btn.textContent = state.lisajasen.added ? 'Lisätty' : 'Lisää';
			if (form) form.hidden = ! state.lisajasen.added;
			saveState(state);
			renderSummary(state);
		});

		// Lomakekentät
		card.querySelectorAll('[data-friend-field]').forEach(function (input) {
			input.addEventListener('input', function () {
				var key = input.getAttribute('data-friend-field');
				if (!state.lisajasen) state.lisajasen = { added: false, firstname: '', lastname: '', email: '' };
				state.lisajasen[key] = input.value;
				saveState(state);
				renderSummary(state); // päivittää nimen yhteenvedossa
			});
		});

		// Renderöi hinta uudelleen aina kun scope tai commitment muuttuu
		// (Renderöi nyt heti)
		renderFriendPrice(state);
	}

	function wirePaymentMethods(state) {
		var all = document.querySelectorAll('.sp-payment-method');
		if (!all.length) return;

		// Yhteenvedon "Maksutapa: X" -rivi
		var sumIcon = document.getElementById('sp-sum-pay-icon');
		var sumName = document.getElementById('sp-sum-pay-name');
		var sumRow  = document.getElementById('sp-summary-payment');

		function updateSummary(el) {
			if (!sumName) return;
			var label = el.getAttribute('aria-label') || el.textContent.trim();
			var iconEl = el.querySelector('.sp-payment-method-icon');
			sumName.textContent = label;
			if (sumIcon) sumIcon.innerHTML = iconEl ? iconEl.innerHTML : '';
		}

		function setActive(el) {
			all.forEach(function (other) {
				other.classList.remove('is-active');
				other.setAttribute('aria-checked', 'false');
			});
			el.classList.add('is-active');
			el.setAttribute('aria-checked', 'true');
			state.payment = el.getAttribute('data-method');
			saveState(state);
			updateSummary(el);
		}

		// Palauta valittu maksutapa, fallback ensimmäiseen
		var matchEl = null;
		all.forEach(function (m) {
			if (m.getAttribute('data-method') === state.payment) matchEl = m;
		});
		if (!matchEl) matchEl = all[0];
		setActive(matchEl);

		all.forEach(function (m) {
			m.addEventListener('click', function () { setActive(m); });
		});

		// Klikkaus "Maksutapa"-rivin päälle yhteenvedossa → scrollaa maksutapaosioon
		if (sumRow) {
			sumRow.addEventListener('click', function () {
				var target = document.getElementById(sumRow.getAttribute('data-scroll-to'));
				if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			});
		}
	}

	function onReady(fn) {
		if (document.readyState !== 'loading') return fn();
		document.addEventListener('DOMContentLoaded', fn);
	}

	onReady(function () {
		var state = loadState();

		// /liity-sivu
		if (document.getElementById('sp-cards') && DATA) {
			wireLiityScope(state);
			renderLiity(state);
		}

		// Modaali (vain /liity:llä)
		if (document.getElementById('sp-classes-modal')) {
			wireClassesModal();
		}

		// /jatka: login-valinta
		if (document.getElementById('sp-login-choice')) {
			wireRekisterointi(state);
		}

		// /jatka: yhteystiedot-lomake + huoltajakentät (alaikäiselle) + validointi
		if (document.getElementById('sp-guest-form')) {
			wireCustomerForm(state);
			wireGuardianFields(state);
			wireValidation();
		}

		// Yleinen info-ikoni → selitys-laatikon toggle (esim. "miksi kysymme")
		wireInfoToggles();

		// Salasana-kenttien näytä/piilota-nappi
		wirePasswordToggles();

		// Yhteinen tilauskooste
		if (document.getElementById('sp-sum-rows') && DATA) {
			renderSummary(state);
		}

		// /maksu: upsellit + maksutapa + lisäjäsen
		if (document.querySelector('.sp-upsell')) {
			wireUpsells(state);
		}
		if (document.querySelector('.sp-upsell-friend')) {
			wireLisajasen(state);
		}
		if (document.querySelector('.sp-payment-method')) {
			wirePaymentMethods(state);
		}
	});
})();
